<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\XlsxExtractor;
use DokuWikiTest;

/**
 * Tests for the XLSX extractor.
 *
 * @group plugin_totext
 */
class XlsxExtractorTest extends DokuWikiTest
{
    /** @var string[] */
    protected $pluginsEnabled = ['totext'];

    /** @var string temp working directory */
    private $tmp = '';

    /** @var string fixture path */
    private $fixture = '';

    /** @inheritDoc */
    public function setUp(): void
    {
        parent::setUp();
        $this->tmp = FixtureBuilder::tempDir();
        $this->fixture = $this->tmp . '/sample.xlsx';
        FixtureBuilder::buildXlsx($this->fixture);
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        FixtureBuilder::cleanup($this->tmp);
        parent::tearDown();
    }

    public function testIncludesSheetHeader()
    {
        $text = (new XlsxExtractor())->extract($this->fixture);
        $this->assertStringContainsString('=== Sheet: Data ===', $text);
    }

    public function testResolvesSharedStrings()
    {
        $text = (new XlsxExtractor())->extract($this->fixture);
        $this->assertStringContainsString("Hello\tWorld", $text);
    }

    public function testIncludesRawAndInlineValues()
    {
        $text = (new XlsxExtractor())->extract($this->fixture);
        $this->assertStringContainsString("42\tinline", $text);
    }

    public function testFollowsWorkbookOrderAndPairsNamesViaRelationships()
    {
        // Fixture: tab order is "Beta" (stored in sheet2.xml) then "Alpha"
        // (stored in sheet1.xml). A correct extractor resolves names to files
        // through xl/_rels/workbook.xml.rels rather than pairing sorted file
        // names with names positionally.
        $path = $this->tmp . '/multi.xlsx';
        FixtureBuilder::buildMultiSheetXlsx($path);
        $text = (new XlsxExtractor())->extract($path);

        // Beta must come first and sit above its own content, Alpha second.
        $this->assertStringContainsString("=== Sheet: Beta ===\nBetaCell", $text);
        $this->assertStringContainsString("=== Sheet: Alpha ===\nAlphaCell", $text);
        $this->assertLessThan(
            strpos($text, '=== Sheet: Alpha ==='),
            strpos($text, '=== Sheet: Beta ==='),
        );
    }

    public function testFallsBackToSheetNumberWhenWorkbookMissing()
    {
        $path = $this->tmp . '/nowb.xlsx';
        FixtureBuilder::buildXlsxNoWorkbook($path);
        $text = (new XlsxExtractor())->extract($path);
        $this->assertStringContainsString('=== Sheet: Sheet1 ===', $text);
        $this->assertStringContainsString('Orphan', $text);
    }

    public function testNoWorksheetsThrows()
    {
        $path = $this->tmp . '/empty.xlsx';
        FixtureBuilder::buildXlsxNoWorksheets($path);
        $this->expectException(ExtractionException::class);
        (new XlsxExtractor())->extract($path);
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new XlsxExtractor())->extract($this->tmp . '/nope.xlsx');
    }

    /**
     * @return array<string, array{0: string, 1: bool}>
     */
    public function provideSupports(): array
    {
        return [
            'xlsx' => ['foo.xlsx', true],
            'uppercase' => ['foo.XLSX', true],
            'docx' => ['foo.docx', false],
            'legacy xls' => ['foo.xls', false],
        ];
    }

    /**
     * @dataProvider provideSupports
     */
    public function testSupports(string $path, bool $expected)
    {
        $this->assertSame($expected, (new XlsxExtractor())->supports($path));
    }
}
