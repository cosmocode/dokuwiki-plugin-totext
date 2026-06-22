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
        $text = (new OdsExtractor())->extract(Samples::path('tika-sample.ods'))->text;
        $this->assertStringContainsString('=== Sheet: Sheet1 ===', $text);
    }

    public function testEmitsTabSeparatedCells()
    {
        // cells within a row are joined with tabs
        $text = (new OdsExtractor())->extract(Samples::path('tika-sample.ods'))->text;
        $this->assertStringContainsString("This\tis\tan\texample\tspreadsheet", $text);
    }

    public function testExtractsMetaXmlMetadata()
    {
        $meta = (new OdsExtractor())->extract(Samples::path('tika-sample.ods'))->metadata;
        $this->assertArrayHasKey('Created', $meta);
        $this->assertArrayHasKey('Modified', $meta);
        $this->assertStringContainsString('LibreOffice', $meta['Producer']);
    }

    public function testMissingContentRecordsTextErrorButKeepsMetadata()
    {
        // a real ODS with content.xml removed: meta.xml is a separate part, so
        // text extraction fails (recorded) while the metadata is still salvaged.
        $broken = Samples::withoutPart('tika-sample.ods', 'content.xml');
        $result = (new OdsExtractor())->extract($broken);
        $this->assertInstanceOf(ExtractionException::class, $result->textError);
        $this->assertSame('', $result->text);
        $this->assertNull($result->metadataError);
        $this->assertStringContainsString('LibreOffice', $result->metadata['Producer']);
    }
}
