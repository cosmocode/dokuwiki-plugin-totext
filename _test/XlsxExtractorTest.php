<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\XlsxExtractor;
use DokuWikiTest;

/**
 * Tests for the XLSX extractor, run against real LibreOffice output.
 *
 * @group plugin_totext
 */
class XlsxExtractorTest extends DokuWikiTest
{
    /** @var string[] */
    protected $pluginsEnabled = ['totext'];

    /** @var string temp working directory */
    private $tmp = '';

    /** @inheritDoc */
    public function setUp(): void
    {
        parent::setUp();
        $this->tmp = Samples::tempDir();
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        Samples::cleanup($this->tmp);
        parent::tearDown();
    }

    public function testIncludesSheetHeaderAndTabSeparatedCells()
    {
        $text = (new XlsxExtractor())->extract(Samples::path('sample.xlsx'));
        $this->assertStringContainsString('=== Sheet: ', $text);
        $this->assertStringContainsString("Widget\t42", $text);
    }

    public function testFollowsTabOrderAndResolvesSheetNames()
    {
        // multi-sheet.xlsx has tabs "Beta" then "Alpha"; the extractor must
        // resolve each sheet name to its worksheet file via the relationships
        // and emit them in tab order.
        $text = (new XlsxExtractor())->extract(Samples::path('multi-sheet.xlsx'));
        $this->assertStringContainsString("=== Sheet: Beta ===\nBetaCell", $text);
        $this->assertStringContainsString("=== Sheet: Alpha ===\nAlphaCell", $text);
        $this->assertLessThan(
            strpos($text, '=== Sheet: Alpha ==='),
            strpos($text, '=== Sheet: Beta ==='),
        );
    }

    public function testFallsBackToSheetNumberWhenWorkbookMissing()
    {
        // a real XLSX whose xl/workbook.xml is gone: names can no longer be
        // resolved, so the extractor falls back to positional "SheetN" naming
        $broken = Samples::withoutPart('sample.xlsx', 'xl/workbook.xml', $this->tmp);
        $text = (new XlsxExtractor())->extract($broken);
        $this->assertStringContainsString('=== Sheet: Sheet1 ===', $text);
        $this->assertStringContainsString('Widget', $text);
    }

    public function testCorruptContainerThrows()
    {
        $this->expectException(ExtractionException::class);
        (new XlsxExtractor())->extract(Samples::corrupt($this->tmp . '/corrupt.xlsx'));
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new XlsxExtractor())->extract($this->tmp . '/nope.xlsx');
    }
}
