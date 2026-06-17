<?php

namespace dokuwiki\plugin\totext\Extractor;

use dokuwiki\plugin\totext\Exception\ExtractionException;

/**
 * Extracts text from Word (.docx) documents.
 */
class DocxExtractor extends AbstractZipXmlExtractor
{
    /** @inheritDoc */
    protected function extractText(): string
    {
        $doc = $this->readPart('word/document.xml');
        if ($doc === null) {
            throw new ExtractionException('Not a valid DOCX file: missing word/document.xml');
        }

        $parts = [
            $this->extractDocxText($doc),
        ];

        foreach ($this->listParts('word/header') as $headerPath) {
            if (str_ends_with($headerPath, '.xml')) {
                $xml = $this->readPart($headerPath);
                if ($xml !== null) {
                    $parts[] = $this->extractDocxText($xml);
                }
            }
        }
        foreach ($this->listParts('word/footer') as $footerPath) {
            if (str_ends_with($footerPath, '.xml')) {
                $xml = $this->readPart($footerPath);
                if ($xml !== null) {
                    $parts[] = $this->extractDocxText($xml);
                }
            }
        }

        return trim(implode("\n", array_filter($parts, fn($p) => $p !== '')));
    }

    /**
     * Walk the WordprocessingML text model of a single part.
     *
     * @param string $xml the part XML
     * @return string
     */
    protected function extractDocxText(string $xml): string
    {
        return $this->extractTextFromXml(
            $xml,
            textElement: 't',
            blockElements: ['p', 'br'],
            tabElements: ['tab'],
        );
    }
}
