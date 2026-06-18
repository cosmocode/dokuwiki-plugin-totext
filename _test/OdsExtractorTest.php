<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\OdsExtractor;
use DokuWikiTest;

/**
 * Tests for the ODS extractor, run against a real Apache Tika sample spreadsheet.
 *
 * @group plugin_totext
 */
class OdsExtractorTest extends DokuWikiTest
{
    /** @var string[] */
    protected $pluginsEnabled = ['totext'];

    public function testIncludesSheetHeader()
    {
        $text = (new OdsExtractor())->extract(Samples::path('tika-sample.ods'));
        $this->assertStringContainsString('=== Sheet: Sheet1 ===', $text);
    }

    public function testEmitsTabSeparatedCells()
    {
        // cells within a row are joined with tabs
        $text = (new OdsExtractor())->extract(Samples::path('tika-sample.ods'));
        $this->assertStringContainsString("This\tis\tan\texample\tspreadsheet", $text);
    }

    public function testMissingContentThrows()
    {
        // a real ODS with its content.xml removed
        $broken = Samples::withoutPart('tika-sample.ods', 'content.xml');
        $this->expectException(ExtractionException::class);
        (new OdsExtractor())->extract($broken);
    }
}
