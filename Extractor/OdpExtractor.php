<?php

namespace dokuwiki\plugin\totext\Extractor;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use XMLReader;

/**
 * Extracts text from OpenDocument Presentation (.odp) files.
 *
 * Each draw:page is treated as a slide and emitted under a "=== Slide N ==="
 * header, mirroring the PPTX output.
 */
final class OdpExtractor extends AbstractZipXmlExtractor
{
    /** @inheritDoc */
    protected function extension(): string
    {
        return 'odp';
    }

    /** @inheritDoc */
    protected function extractText(): string
    {
        $content = $this->readPart('content.xml');
        if ($content === null) {
            throw new ExtractionException('Not a valid ODP file: missing content.xml');
        }

        $reader = new XMLReader();
        if (!$reader->XML($content, 'UTF-8', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            throw new ExtractionException('Failed to parse content.xml');
        }

        try {
            $out = [];
            $slide = 0;
            while ($reader->read()) {
                if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 'page') {
                    $slide++;
                    $body = $this->extractPage($reader);
                    $out[] = "=== Slide $slide ===\n" . $body;
                }
            }
            return trim(implode("\n", $out));
        } finally {
            $reader->close();
        }
    }

    /**
     * Collect text from a single <draw:page> element.
     *
     * @param XMLReader $reader positioned on the opening <draw:page> element
     * @return string
     */
    private function extractPage(XMLReader $reader)
    {
        if ($reader->isEmptyElement) {
            return '';
        }
        $out = '';
        while ($reader->read()) {
            $nt = $reader->nodeType;
            $local = $reader->localName;
            if ($nt === XMLReader::TEXT || $nt === XMLReader::CDATA || $nt === XMLReader::SIGNIFICANT_WHITESPACE) {
                $out .= $reader->value;
            } elseif ($nt === XMLReader::ELEMENT) {
                if ($local === 'p' || $local === 'h' || $local === 'line-break') {
                    // drop any indentation whitespace captured before the block
                    $out = rtrim($out, " \t");
                    if ($out !== '' && !str_ends_with($out, "\n")) {
                        $out .= "\n";
                    }
                } elseif ($local === 'tab') {
                    $out .= "\t";
                }
            } elseif ($nt === XMLReader::END_ELEMENT && $local === 'page') {
                break;
            }
        }
        return trim($out);
    }
}
