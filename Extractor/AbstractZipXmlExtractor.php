<?php

namespace dokuwiki\plugin\totext\Extractor;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\Utf8\Clean;
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
    /** maximum total size in bytes of all files unpacked from one archive */
    protected const MAX_EXTRACTION_SIZE = 100 * 1024 * 1024;

    /** maximum number of files and directories unpacked from one archive */
    protected const MAX_EXTRACTION_FILES = 10000;

    /** maximum size in bytes of the extracted text, longer text is truncated */
    protected const MAX_TEXT_SIZE = 10 * 1024 * 1024;

    /** @var string path to the temp dir the archive was extracted into */
    protected string $tempDir = '';

    /** @var int number of bytes that appendText() can still add before the text limit is reached */
    protected int $textBudget = 0;

    /** @var array<string, true>|null temp dirs to remove at shutdown, null until the shutdown function is registered */
    protected static ?array $shutdownTempDirs = null;

    /**
     * Extract text from the already-unpacked archive (in $this->tempDir).
     *
     * @return string
     */
    abstract protected function extractText(): string;

    /**
     * Extract the canonical metadata map from the already-unpacked archive.
     *
     * Implemented per format family (see AbstractOoxmlExtractor /
     * AbstractOdfExtractor), reading the family's metadata part(s) from
     * $this->tempDir. Best-effort: a missing or broken metadata part yields an
     * empty array rather than throwing, so the body text is still returned.
     *
     * @return array<string, string> canonical key => value map
     */
    abstract protected function extractMetadata(): array;

    /** @inheritDoc */
    public function extract(string $path): ExtractionResult
    {
        if (!is_file($path)) {
            throw new ExtractionException("File not found: $path");
        }

        $this->tempDir = $this->makeTempDir();
        try {
            // Opening the container is the total-failure gate: if the archive
            // cannot be unpacked, nothing is recoverable and we throw.
            try {
                $zip = new Zip();
                // older php-archive versions bundled with DokuWiki have no extraction limits
                if (method_exists($zip, 'setExtractionSizeLimit')) {
                    $zip->setExtractionSizeLimit(static::MAX_EXTRACTION_SIZE);
                    $zip->setExtractionFileLimit(static::MAX_EXTRACTION_FILES);
                }
                $zip->open($path);
                $zip->extract($this->tempDir);
                $zip->close();
            } catch (\Throwable $e) {
                throw ExtractionException::wrap($e, "Failed to open $path");
            }

            // The container opened: text and metadata are independent halves,
            // each recorded so a failure in one never discards the other.
            $text = '';
            $textError = null;
            $this->textBudget = static::MAX_TEXT_SIZE;
            try {
                $text = $this->cutText($this->extractText(), static::MAX_TEXT_SIZE);
            } catch (\Throwable $e) {
                $textError = ExtractionException::wrap($e, "Failed to extract text from $path");
            }

            $metadata = [];
            $metadataError = null;
            try {
                $metadata = $this->extractMetadata();
            } catch (\Throwable $e) {
                $metadataError = ExtractionException::wrap($e, "Failed to extract metadata from $path");
            }

            return new ExtractionResult($text, $metadata, $textError, $metadataError);
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
        $base = fullpath($this->tempDir) . '/';
        $full = fullpath($base . $internalPath);
        if (!str_starts_with($full, $base) || !is_file($full)) {
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
     * Stream-parse an XML metadata part into a canonical key => value map.
     *
     * Walks the document and, for every element whose local name is a key in
     * $map, captures its text content under the mapped canonical key. Matching
     * is by local name only (namespace-agnostic), which is enough because the
     * metadata vocabularies (OOXML core/app, ODF meta) use distinct local
     * names. Empty values are dropped. Local names listed (by their canonical
     * key) in $multiValueKeys accumulate every occurrence, space-joined; all
     * others keep the last non-empty occurrence.
     *
     * Best-effort: returns an empty array if the XML cannot be parsed.
     *
     * @param string $xml the metadata part XML
     * @param array<string, string> $map element local name => canonical key
     * @param string[] $multiValueKeys canonical keys whose values accumulate
     * @return array<string, string> canonical key => value map
     */
    protected function mapMetadataFromXml(string $xml, array $map, array $multiValueKeys = []): array
    {
        $reader = new XMLReader();
        if (!$reader->XML($xml, 'UTF-8', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            return [];
        }
        $multi = array_flip($multiValueKeys);
        $out = [];
        try {
            while ($reader->read()) {
                if ($reader->nodeType !== XMLReader::ELEMENT) {
                    continue;
                }
                $key = $map[$reader->localName] ?? null;
                if ($key === null) {
                    continue;
                }
                $value = trim($this->readElementText($reader));
                if ($value === '') {
                    continue;
                }
                if (isset($multi[$key]) && isset($out[$key])) {
                    $out[$key] .= ' ' . $value;
                } else {
                    $out[$key] = $value;
                }
            }
        } finally {
            $reader->close();
        }
        return $out;
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
            while ($this->textBudget > 0 && $reader->read()) {
                if ($reader->nodeType !== XMLReader::ELEMENT) {
                    continue;
                }
                $local = $reader->localName;
                if ($local === $textElement) {
                    $this->appendText($out, $this->readElementText($reader));
                } elseif (isset($blocks[$local])) {
                    if ($out !== '' && !str_ends_with($out, "\n")) {
                        $this->appendText($out, "\n");
                    }
                } elseif (isset($tabs[$local])) {
                    $this->appendText($out, "\t");
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
            while ($this->textBudget > 0 && $reader->read()) {
                $nt = $reader->nodeType;
                if ($nt === XMLReader::TEXT || $nt === XMLReader::CDATA || $nt === XMLReader::SIGNIFICANT_WHITESPACE) {
                    $this->appendText($out, $reader->value);
                } elseif ($nt === XMLReader::ELEMENT) {
                    $local = $reader->localName;
                    if (isset($blocks[$local])) {
                        // drop any indentation whitespace captured before the block
                        $out = rtrim($out, " \t");
                        if ($out !== '' && !str_ends_with($out, "\n")) {
                            $this->appendText($out, "\n");
                        }
                    } elseif (isset($tabs[$local])) {
                        $this->appendText($out, "\t");
                    }
                }
            }
            return $out;
        } finally {
            $reader->close();
        }
    }

    /**
     * Read the text content of the current element and move the reader to its end.
     *
     * The reader stops on the closing element, so the caller does not visit
     * nested elements again and reads nested text only once.
     *
     * @param XMLReader $reader positioned on an opening element
     * @return string the text of the element and all its descendants
     */
    protected function readElementText(XMLReader $reader): string
    {
        if ($reader->isEmptyElement) {
            return '';
        }
        $depth = $reader->depth;
        $text = '';
        while ($reader->read() && $reader->depth > $depth) {
            $nt = $reader->nodeType;
            if (
                $nt === XMLReader::TEXT || $nt === XMLReader::CDATA ||
                $nt === XMLReader::WHITESPACE || $nt === XMLReader::SIGNIFICANT_WHITESPACE
            ) {
                $text .= $reader->value;
            }
        }
        return $text;
    }

    /**
     * Append text to an output buffer, within the remaining text budget.
     *
     * When the text does not fit, it is cut at a character boundary and the
     * budget is set to zero, so all further text is dropped.
     *
     * @param string $out the output buffer to append to
     * @param string $text the text to append
     * @return void
     */
    protected function appendText(string &$out, string $text): void
    {
        if (strlen($text) >= $this->textBudget) {
            $text = $this->cutText($text, $this->textBudget);
            $this->textBudget = 0;
        } else {
            $this->textBudget -= strlen($text);
        }
        $out .= $text;
    }

    /**
     * Cut a UTF-8 string to a maximum byte length at a character boundary.
     *
     * @param string $text the UTF-8 text
     * @param int $length the maximum length in bytes
     * @return string the text, or its longest prefix that fits
     */
    protected function cutText(string $text, int $length): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, Clean::correctIdx($text, $length));
    }

    /**
     * Create a private temporary directory for unpacking the archive.
     *
     * Uses DokuWiki's temp dir ($conf['tmpdir']) via the core io_mktmpdir()
     * helper rather than the system temp dir. A shutdown function removes the
     * directory if a fatal error stops the extraction before cleanup().
     *
     * @return string the created directory path
     * @throws ExtractionException if the directory cannot be created
     */
    protected function makeTempDir(): string
    {
        $dir = io_mktmpdir();
        if ($dir === false) {
            throw new ExtractionException('Could not create temp dir');
        }

        // a fatal error skips finally blocks and destructors, but not shutdown functions
        if (self::$shutdownTempDirs === null) {
            self::$shutdownTempDirs = [];
            register_shutdown_function(static function () {
                foreach (array_keys(self::$shutdownTempDirs) as $tempDir) {
                    io_rmdir($tempDir, true);
                }
            });
        }
        self::$shutdownTempDirs[$dir] = true;

        return $dir;
    }

    /**
     * Recursively remove the unpack temp dir and reset the tracking property.
     *
     * Safe to call repeatedly and when no temp dir is set.
     *
     * @return void
     */
    protected function cleanup(): void
    {
        if ($this->tempDir === '') {
            return;
        }
        io_rmdir($this->tempDir, true);
        unset(self::$shutdownTempDirs[$this->tempDir]);
        $this->tempDir = '';
    }
}
