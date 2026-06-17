<?php

namespace dokuwiki\plugin\totext\Extractor;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use splitbrain\PHPArchive\Zip;
use XMLReader;

/**
 * Base class for extractors that read a ZIP container of XML parts.
 *
 * This covers both OOXML (DOCX/XLSX/PPTX) and OpenDocument (ODT/ODS/ODP),
 * which all package their content as XML inside a ZIP archive. Subclasses
 * declare their extension and implement extractText() using readPart(),
 * listParts() and the XML text-walking helpers provided here.
 */
abstract class AbstractZipXmlExtractor implements ExtractorInterface
{
    /** @var string path to the temp dir the archive was extracted into */
    private string $tempDir = '';

    /**
     * Extract text from the already-unpacked archive (in $this->tempDir).
     *
     * @return string
     */
    abstract protected function extractText(): string;

    /**
     * Clean up any leftover temp dir when the instance is destroyed.
     *
     * extract() removes the temp dir promptly in its finally block; this acts
     * as a safety net so a dir is still removed if the process dies (e.g. on a
     * fatal error) before that block runs.
     */
    public function __destruct()
    {
        $this->cleanup();
    }

    /** @inheritDoc */
    public function extract(string $path): string
    {
        if (!is_file($path)) {
            throw new ExtractionException("File not found: $path");
        }

        $this->tempDir = $this->makeTempDir();
        try {
            $zip = new Zip();
            $zip->open($path);
            $zip->extract($this->tempDir);
            $zip->close();

            return $this->extractText();
        } catch (ExtractionException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new ExtractionException(
                "Failed to extract text from $path: " . $e->getMessage(),
                0,
                $e,
            );
        } finally {
            $this->cleanup();
        }
    }

    /**
     * Read a single part from the unpacked archive.
     *
     * @param string $internalPath path relative to the archive root
     * @return string|null the part content, or null if it does not exist
     */
    protected function readPart(string $internalPath): ?string
    {
        $full = $this->tempDir . '/' . ltrim($internalPath, '/');
        if (!is_file($full)) {
            return null;
        }
        $data = file_get_contents($full);
        return $data === false ? null : $data;
    }

    /**
     * List all parts whose internal path starts with the given prefix.
     *
     * @param string $prefix internal path prefix to match
     * @return string[] internal paths (relative to archive root), naturally sorted
     */
    protected function listParts(string $prefix): array
    {
        if (!is_dir($this->tempDir)) {
            return [];
        }
        $base = $this->tempDir . '/';
        $results = [];
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->tempDir, FilesystemIterator::SKIP_DOTS),
        );
        foreach ($it as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $rel = str_replace('\\', '/', substr($file->getPathname(), strlen($base)));
            if (str_starts_with($rel, $prefix)) {
                $results[] = $rel;
            }
        }
        sort($results, SORT_NATURAL);
        return $results;
    }

    /**
     * Stream-parse XML and concatenate text from elements matching $textElement.
     *
     * Used by OOXML formats where runs of text live in a known wrapper element
     * (e.g. <w:t>, <a:t>). Block elements emit a newline; tab elements emit a tab.
     *
     * @param string $xml the XML document
     * @param string $textElement local name of the text-carrying element
     * @param string[] $blockElements local names that should emit a newline
     * @param string[] $tabElements local names that should emit a tab
     * @return string
     * @throws ExtractionException if the XML cannot be parsed
     */
    protected function extractTextFromXml(
        string $xml,
        string $textElement,
        array $blockElements = [],
        array $tabElements = [],
    ): string {
        $reader = new XMLReader();
        if (!$reader->XML($xml, 'UTF-8', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            throw new ExtractionException('Failed to parse XML');
        }
        try {
            $out = '';
            $blocks = array_flip($blockElements);
            $tabs = array_flip($tabElements);
            while ($reader->read()) {
                if ($reader->nodeType !== XMLReader::ELEMENT) {
                    continue;
                }
                $local = $reader->localName;
                if ($local === $textElement) {
                    $out .= $reader->readString();
                } elseif (isset($blocks[$local])) {
                    if ($out !== '' && !str_ends_with($out, "\n")) {
                        $out .= "\n";
                    }
                } elseif (isset($tabs[$local])) {
                    $out .= "\t";
                }
            }
            return $out;
        } finally {
            $reader->close();
        }
    }

    /**
     * Stream-parse XML and concatenate ALL character data.
     *
     * Used by OpenDocument formats, which store text directly as character
     * data inside paragraph elements rather than in a single wrapper element.
     * Block elements emit a newline; tab elements emit a tab.
     *
     * @param string $xml the XML document
     * @param string[] $blockElements local names that should emit a newline
     * @param string[] $tabElements local names that should emit a tab
     * @return string
     * @throws ExtractionException if the XML cannot be parsed
     */
    protected function extractAllTextFromXml(
        string $xml,
        array $blockElements = [],
        array $tabElements = [],
    ): string {
        $reader = new XMLReader();
        if (!$reader->XML($xml, 'UTF-8', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            throw new ExtractionException('Failed to parse XML');
        }
        try {
            $out = '';
            $blocks = array_flip($blockElements);
            $tabs = array_flip($tabElements);
            while ($reader->read()) {
                $nt = $reader->nodeType;
                if ($nt === XMLReader::TEXT || $nt === XMLReader::CDATA || $nt === XMLReader::SIGNIFICANT_WHITESPACE) {
                    $out .= $reader->value;
                } elseif ($nt === XMLReader::ELEMENT) {
                    $local = $reader->localName;
                    if (isset($blocks[$local])) {
                        // drop any indentation whitespace captured before the block
                        $out = rtrim($out, " \t");
                        if ($out !== '' && !str_ends_with($out, "\n")) {
                            $out .= "\n";
                        }
                    } elseif (isset($tabs[$local])) {
                        $out .= "\t";
                    }
                }
            }
            return $out;
        } finally {
            $reader->close();
        }
    }

    /**
     * Create a private temporary directory for unpacking the archive.
     *
     * Uses DokuWiki's temp dir ($conf['tmpdir']) via the core io_mktmpdir()
     * helper rather than the system temp dir.
     *
     * @return string the created directory path
     * @throws ExtractionException if the directory cannot be created
     */
    private function makeTempDir(): string
    {
        $dir = io_mktmpdir();
        if ($dir === false) {
            throw new ExtractionException('Could not create temp dir');
        }
        return $dir;
    }

    /**
     * Recursively remove the unpack temp dir and reset the tracking property.
     *
     * Safe to call repeatedly and when no temp dir is set (the destructor and
     * extract()'s finally block both call it).
     *
     * @return void
     */
    private function cleanup(): void
    {
        if ($this->tempDir === '') {
            return;
        }
        io_rmdir($this->tempDir, true);
        $this->tempDir = '';
    }
}
