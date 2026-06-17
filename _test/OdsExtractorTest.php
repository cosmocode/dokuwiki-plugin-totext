<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\OdsExtractor;
use DokuWikiTest;

/**
 * Tests for the ODS extractor, run against real LibreOffice output.
 *
 * @group plugin_totext
 */
class OdsExtractorTest extends DokuWikiTest
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

    public function testIncludesSheetHeader()
    {
        $text = (new OdsExtractor())->extract(Samples::path('rich.ods'));
        $this->assertStringContainsString('=== Sheet: Edge ===', $text);
    }

    public function testMergedCellEmitsCoveredColumnsAsTabs()
    {
        // "Merged" spans three columns; LibreOffice stores the two extra columns
        // as a covered-cell run, which the extractor renders as empty columns
        $text = (new OdsExtractor())->extract(Samples::path('rich.ods'));
        $this->assertStringContainsString("Merged\t\tAfter", $text);
    }

    public function testMultiParagraphCellAndInCellTab()
    {
        // a cell with two paragraphs joins on a space; an in-cell tab also
        // becomes a space; the two cells stay tab-separated
        $text = (new OdsExtractor())->extract(Samples::path('rich.ods'));
        $this->assertStringContainsString("Line1 Line2\tX Y", $text);
    }

    public function testMissingContentThrows()
    {
        // a real ODS with its content.xml removed
        $broken = Samples::withoutPart('sample.ods', 'content.xml', $this->tmp);
        $this->expectException(ExtractionException::class);
        (new OdsExtractor())->extract($broken);
    }

    public function testCorruptContainerThrows()
    {
        $this->expectException(ExtractionException::class);
        (new OdsExtractor())->extract(Samples::corrupt($this->tmp . '/corrupt.ods'));
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new OdsExtractor())->extract($this->tmp . '/nope.ods');
    }
}
