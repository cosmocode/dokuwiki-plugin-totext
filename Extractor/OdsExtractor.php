<?php

namespace dokuwiki\plugin\totext\Extractor;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use XMLReader;

/**
 * Extracts text from OpenDocument Spreadsheet (.ods) workbooks.
 *
 * Each table is rendered under a "=== Sheet: <name> ===" header with
 * tab-separated cells and newline-separated rows, mirroring the XLSX output.
 */
class OdsExtractor extends AbstractOdfExtractor
{
    /** namespace URI for the OpenDocument table vocabulary */
    protected const TABLE_NS = 'urn:oasis:names:tc:opendocument:xmlns:table:1.0';

    /** safety cap for table:number-columns-repeated to avoid pathological expansion */
    protected const MAX_REPEAT = 1024;

    /** @inheritDoc */
    protected function extractText(): string
    {
        $content = $this->readPart('content.xml');
        if ($content === null) {
            throw new ExtractionException('Not a valid ODS file: missing content.xml');
        }

        $reader = new XMLReader();
        if (!$reader->XML($content, 'UTF-8', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            throw new ExtractionException('Failed to parse content.xml');
        }

        try {
            $out = [];
            $sheetIndex = 0;
            while ($reader->read()) {
                if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 'table') {
                    $sheetIndex++;
                    $name = $reader->getAttributeNs('name', self::TABLE_NS) ?? ('Sheet' . $sheetIndex);
                    $body = $this->extractTable($reader);
                    $out[] = "=== Sheet: $name ===\n" . $body;
                }
            }
            return trim(implode("\n", $out));
        } finally {
            $reader->close();
        }
    }

    /**
     * Render a single <table:table> element as tab-separated rows.
     *
     * @param XMLReader $reader positioned on the opening <table:table> element
     * @return string
     */
    protected function extractTable(XMLReader $reader): string
    {
        if ($reader->isEmptyElement) {
            return '';
        }
        $out = '';
        $rowFirst = true;
        $cellFirst = true;
        while ($reader->read()) {
            $nt = $reader->nodeType;
            $local = $reader->localName;
            if ($nt === XMLReader::ELEMENT && $local === 'table-row') {
                if (!$rowFirst) {
                    $out .= "\n";
                }
                $rowFirst = false;
                $cellFirst = true;
            } elseif ($nt === XMLReader::ELEMENT && ($local === 'table-cell' || $local === 'covered-table-cell')) {
                $repeat = (int) ($reader->getAttributeNs('number-columns-repeated', self::TABLE_NS) ?? '1');
                if ($repeat < 1) {
                    $repeat = 1;
                }
                $value = $this->readCellValue($reader);
                // Only honour repeats for cells that actually carry text; trailing
                // repeated empty cells just pad the grid and would blow up output.
                if ($value !== '') {
                    $repeat = min($repeat, self::MAX_REPEAT);
                    for ($r = 0; $r < $repeat; $r++) {
                        if (!$cellFirst) {
                            $out .= "\t";
                        }
                        $cellFirst = false;
                        $out .= $value;
                    }
                } else {
                    if (!$cellFirst) {
                        $out .= "\t";
                    }
                    $cellFirst = false;
                }
            } elseif ($nt === XMLReader::END_ELEMENT && $local === 'table') {
                break;
            }
        }
        return $out;
    }

    /**
     * Read the text content of a single table cell.
     *
     * @param XMLReader $reader positioned on the opening cell element
     * @return string
     */
    protected function readCellValue(XMLReader $reader): string
    {
        if ($reader->isEmptyElement) {
            return '';
        }
        $cellName = $reader->localName;
        $value = '';
        $paraFirst = true;
        while ($reader->read()) {
            $nt = $reader->nodeType;
            $local = $reader->localName;
            if ($nt === XMLReader::TEXT || $nt === XMLReader::CDATA || $nt === XMLReader::SIGNIFICANT_WHITESPACE) {
                $value .= $reader->value;
            } elseif ($nt === XMLReader::ELEMENT && ($local === 'p' || $local === 'h')) {
                // multiple paragraphs in one cell are separated by a space
                if (!$paraFirst && $value !== '' && !str_ends_with($value, ' ')) {
                    $value .= ' ';
                }
                $paraFirst = false;
            } elseif ($nt === XMLReader::ELEMENT && $local === 'tab') {
                $value .= ' ';
            } elseif ($nt === XMLReader::END_ELEMENT && $local === $cellName) {
                break;
            }
        }
        return trim($value);
    }
}
