<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Extractor\XlsxExtractor;
use DokuWikiTest;

/**
 * Tests for the XLSX extractor, run against a real Apache Tika sample workbook.
 *
 * @group plugin_totext
 */
class XlsxExtractorTest extends DokuWikiTest
{
    /** @var string[] */
    protected $pluginsEnabled = ['totext'];

    public function testIncludesSheetHeaderAndTabSeparatedCells()
    {
        $text = (new XlsxExtractor())->extract(Samples::path('tika-sample.xlsx'));
        $this->assertStringContainsString('=== Sheet: ', $text);
        $this->assertStringContainsString("Number\tSquare", $text);
    }

    public function testResolvesSheetNamesInOrder()
    {
        // sample.xlsx has three named sheets; the extractor resolves each name to
        // its worksheet file via the relationships and emits them in tab order
        $text = (new XlsxExtractor())->extract(Samples::path('tika-sample.xlsx'));
        $this->assertStringContainsString('=== Sheet: Feuil1 ===', $text);
        $this->assertStringContainsString('=== Sheet: Feuil2 ===', $text);
        $this->assertStringContainsString('=== Sheet: Feuil3 ===', $text);
        $this->assertLessThan(
            strpos($text, '=== Sheet: Feuil2 ==='),
            strpos($text, '=== Sheet: Feuil1 ==='),
        );
        $this->assertLessThan(
            strpos($text, '=== Sheet: Feuil3 ==='),
            strpos($text, '=== Sheet: Feuil2 ==='),
        );
    }

    public function testFallsBackToSheetNumberWhenWorkbookMissing()
    {
        // a real XLSX whose xl/workbook.xml is gone: names can no longer be
        // resolved, so the extractor falls back to positional "SheetN" naming
        $broken = Samples::withoutPart('tika-sample.xlsx', 'xl/workbook.xml');
        $text = (new XlsxExtractor())->extract($broken);
        $this->assertStringContainsString('=== Sheet: Sheet1 ===', $text);
        $this->assertStringContainsString('Number', $text);
    }
}
