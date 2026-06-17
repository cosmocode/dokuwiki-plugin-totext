<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\OdsExtractor;
use DokuWikiTest;

/**
 * Tests for the ODS extractor.
 *
 * @group plugin_totext
 */
class OdsExtractorTest extends DokuWikiTest
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
        $this->fixture = $this->tmp . '/sample.ods';
        FixtureBuilder::buildOds($this->fixture);
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        FixtureBuilder::cleanup($this->tmp);
        parent::tearDown();
    }

    public function testIncludesSheetHeader()
    {
        $text = (new OdsExtractor())->extract($this->fixture);
        $this->assertStringContainsString('=== Sheet: Data ===', $text);
    }

    public function testRendersTabSeparatedRows()
    {
        $text = (new OdsExtractor())->extract($this->fixture);
        $this->assertStringContainsString("Hello\tWorld", $text);
        $this->assertStringContainsString("42\tinline", $text);
    }

    public function testRepeatedColumnsExpandTextCells()
    {
        $path = $this->tmp . '/rich.ods';
        FixtureBuilder::buildOdsRich($path);
        $text = (new OdsExtractor())->extract($path);
        // a text cell with number-columns-repeated="3" expands to three columns
        $this->assertStringContainsString("A\tA\tA\tB", $text);
    }

    public function testTrailingEmptyRepeatDoesNotExpand()
    {
        $path = $this->tmp . '/rich.ods';
        FixtureBuilder::buildOdsRich($path);
        $text = (new OdsExtractor())->extract($path);
        // an *empty* cell with number-columns-repeated="5" occupies a single
        // column only — it must not pad the grid with five tabs
        $this->assertStringContainsString("Line1 Line2\t\tEnd", $text);
    }

    public function testMultiParagraphCellAndInCellTab()
    {
        $path = $this->tmp . '/rich.ods';
        FixtureBuilder::buildOdsRich($path);
        $text = (new OdsExtractor())->extract($path);
        // covered cells contribute their text; an in-cell tab becomes a space
        $this->assertStringContainsString("Merged\tX Y", $text);
    }

    public function testUnnamedSheetFallsBackToSheetNumber()
    {
        $path = $this->tmp . '/rich.ods';
        FixtureBuilder::buildOdsRich($path);
        $text = (new OdsExtractor())->extract($path);
        $this->assertStringContainsString('=== Sheet: Sheet2 ===', $text);
        $this->assertStringContainsString('Unnamed sheet', $text);
    }

    public function testHugeColumnRepeatIsCapped()
    {
        $path = $this->tmp . '/huge.ods';
        FixtureBuilder::buildOdsHugeRepeat($path, 100000);
        $text = (new OdsExtractor())->extract($path);
        // MAX_REPEAT caps expansion at 1024 regardless of the declared repeat
        $this->assertSame(1024, substr_count($text, 'Z'));
    }

    public function testMissingContentThrows()
    {
        $path = $this->tmp . '/nocontent.ods';
        FixtureBuilder::zip($path, ['mimetype' => 'application/vnd.oasis.opendocument.spreadsheet']);
        $this->expectException(ExtractionException::class);
        (new OdsExtractor())->extract($path);
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new OdsExtractor())->extract($this->tmp . '/nope.ods');
    }

    /**
     * @return array<string, array{0: string, 1: bool}>
     */
    public function provideSupports(): array
    {
        return [
            'ods' => ['foo.ods', true],
            'uppercase' => ['foo.ODS', true],
            'xlsx' => ['foo.xlsx', false],
        ];
    }

    /**
     * @dataProvider provideSupports
     */
    public function testSupports(string $path, bool $expected)
    {
        $this->assertSame($expected, (new OdsExtractor())->supports($path));
    }
}
