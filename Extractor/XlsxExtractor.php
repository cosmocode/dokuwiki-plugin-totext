<?php

namespace dokuwiki\plugin\totext\Extractor;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use XMLReader;

/**
 * Extracts text from Excel (.xlsx) workbooks.
 *
 * Each sheet is rendered under a "=== Sheet: <name> ===" header with
 * tab-separated cells and newline-separated rows.
 */
final class XlsxExtractor extends AbstractZipXmlExtractor
{
    /** namespace URI for relationship references (the r: prefix) */
    private const RELS_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

    /** @inheritDoc */
    protected function extractText(): string
    {
        $sharedStrings = $this->loadSharedStrings();

        $sheets = $this->loadSheets();
        if ($sheets === []) {
            throw new ExtractionException('Not a valid XLSX file: no worksheets found');
        }

        $out = [];
        foreach ($sheets as $sheet) {
            $xml = $this->readPart($sheet['path']);
            if ($xml === null) {
                continue;
            }
            $body = $this->extractSheet($xml, $sharedStrings);
            $out[] = "=== Sheet: {$sheet['name']} ===\n" . $body;
        }

        return trim(implode("\n", $out));
    }

    /**
     * Resolve the worksheets to render, in display (tab) order, each paired with
     * its display name.
     *
     * The authoritative order and name↔file mapping lives in xl/workbook.xml plus
     * xl/_rels/workbook.xml.rels: each <sheet> carries a name and an r:id that the
     * relationships resolve to a worksheet part. Worksheet file numbering does not
     * have to match tab order, so we must follow the relationships rather than
     * pairing sorted filenames with names positionally. When the workbook or its
     * relationships are missing we fall back to filename order with positional or
     * synthesised names.
     *
     * @return array<int, array{name: string, path: string}>
     */
    private function loadSheets(): array
    {
        $rels = $this->loadWorkbookRels();
        if ($rels !== []) {
            $sheets = [];
            foreach ($this->loadWorkbookSheets() as $sheet) {
                $target = $rels[$sheet['rid']] ?? null;
                if ($target === null) {
                    continue;
                }
                $sheets[] = ['name' => $sheet['name'], 'path' => 'xl/' . ltrim($target, '/')];
            }
            if ($sheets !== []) {
                return $sheets;
            }
        }

        // Fallback: pair worksheet files (in filename order) with names positionally.
        $names = $this->loadSheetNames();
        $paths = array_values(array_filter(
            $this->listParts('xl/worksheets/sheet'),
            fn($p) => str_ends_with($p, '.xml'),
        ));
        $sheets = [];
        foreach ($paths as $i => $path) {
            $sheets[] = ['name' => $names[$i] ?? ('Sheet' . ($i + 1)), 'path' => $path];
        }
        return $sheets;
    }

    /**
     * Read the workbook's <sheet> entries as ordered name/relationship-id pairs.
     *
     * @return array<int, array{name: string, rid: string}>
     */
    private function loadWorkbookSheets(): array
    {
        $xml = $this->readPart('xl/workbook.xml');
        if ($xml === null) {
            return [];
        }
        $sheets = [];
        $reader = new XMLReader();
        if (!$reader->XML($xml, 'UTF-8', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            return [];
        }
        try {
            while ($reader->read()) {
                if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 'sheet') {
                    $name = $reader->getAttribute('name');
                    $rid = $reader->getAttributeNs('id', self::RELS_NS);
                    if ($name !== null && $rid !== null) {
                        $sheets[] = ['name' => $name, 'rid' => $rid];
                    }
                }
            }
        } finally {
            $reader->close();
        }
        return $sheets;
    }

    /**
     * Load the workbook relationship map (Id => Target) from
     * xl/_rels/workbook.xml.rels.
     *
     * @return array<string, string>
     */
    private function loadWorkbookRels(): array
    {
        $xml = $this->readPart('xl/_rels/workbook.xml.rels');
        if ($xml === null) {
            return [];
        }
        $rels = [];
        $reader = new XMLReader();
        if (!$reader->XML($xml, 'UTF-8', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            return [];
        }
        try {
            while ($reader->read()) {
                if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 'Relationship') {
                    $id = $reader->getAttribute('Id');
                    $target = $reader->getAttribute('Target');
                    if ($id !== null && $target !== null) {
                        $rels[$id] = $target;
                    }
                }
            }
        } finally {
            $reader->close();
        }
        return $rels;
    }

    /**
     * Load the shared string table (xl/sharedStrings.xml).
     *
     * @return string[] shared strings in index order
     */
    private function loadSharedStrings(): array
    {
        $xml = $this->readPart('xl/sharedStrings.xml');
        if ($xml === null) {
            return [];
        }
        $strings = [];
        $reader = new XMLReader();
        if (!$reader->XML($xml, 'UTF-8', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            return [];
        }
        try {
            while ($reader->read()) {
                if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 'si') {
                    $strings[] = $reader->readString();
                }
            }
        } finally {
            $reader->close();
        }
        return $strings;
    }

    /**
     * Load sheet display names in workbook order (xl/workbook.xml).
     *
     * @return string[] sheet display names
     */
    private function loadSheetNames(): array
    {
        $xml = $this->readPart('xl/workbook.xml');
        if ($xml === null) {
            return [];
        }
        $names = [];
        $reader = new XMLReader();
        if (!$reader->XML($xml, 'UTF-8', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            return [];
        }
        try {
            while ($reader->read()) {
                if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 'sheet') {
                    $name = $reader->getAttribute('name');
                    if ($name !== null) {
                        $names[] = $name;
                    }
                }
            }
        } finally {
            $reader->close();
        }
        return $names;
    }

    /**
     * Render a single worksheet as tab-separated rows.
     *
     * @param string $xml the worksheet XML
     * @param string[] $sharedStrings the shared string table
     * @return string
     * @throws ExtractionException if the worksheet XML cannot be parsed
     */
    private function extractSheet(string $xml, array $sharedStrings): string
    {
        $reader = new XMLReader();
        if (!$reader->XML($xml, 'UTF-8', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            throw new ExtractionException('Failed to parse sheet XML');
        }
        try {
            $out = '';
            $rowFirst = true;
            $cellFirst = true;
            while ($reader->read()) {
                if ($reader->nodeType !== XMLReader::ELEMENT) {
                    continue;
                }
                $local = $reader->localName;
                if ($local === 'row') {
                    if (!$rowFirst) {
                        $out .= "\n";
                    }
                    $rowFirst = false;
                    $cellFirst = true;
                } elseif ($local === 'c') {
                    $type = $reader->getAttribute('t') ?? '';
                    if (!$cellFirst) {
                        $out .= "\t";
                    }
                    $cellFirst = false;
                    $value = $this->readCellValue($reader);
                    if ($type === 's' && is_numeric($value)) {
                        $idx = (int) $value;
                        $value = $sharedStrings[$idx] ?? '';
                    }
                    $out .= $value;
                }
            }
            return $out;
        } finally {
            $reader->close();
        }
    }

    /**
     * Read the value of a single cell element.
     *
     * @param XMLReader $reader positioned on the opening <c> element
     * @return string
     */
    private function readCellValue(XMLReader $reader): string
    {
        if ($reader->isEmptyElement) {
            return '';
        }
        $value = '';
        while ($reader->read()) {
            $nt = $reader->nodeType;
            $ln = $reader->localName;
            if ($nt === XMLReader::ELEMENT && ($ln === 'v' || $ln === 't')) {
                $value .= $reader->readString();
            } elseif ($nt === XMLReader::END_ELEMENT && $ln === 'c') {
                break;
            }
        }
        return $value;
    }
}
