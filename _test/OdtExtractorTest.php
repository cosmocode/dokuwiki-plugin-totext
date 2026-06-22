<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\OdtExtractor;
use DokuWikiTest;

/**
 * Tests for the ODT extractor, run against a real Apache Tika sample document.
 *
 * @group plugin_totext
 */
class OdtExtractorTest extends DokuWikiTest
{
    /** @var string[] */
    protected $pluginsEnabled = ['totext'];

    public function testExtractsBodyText()
    {
        $text = (new OdtExtractor())->extract(Samples::path('tika-sample.odt'))->text;
        $this->assertStringContainsString('This is a sample Open Office document', $text);
        $this->assertStringContainsString('NeoOffice', $text);
    }

    public function testExtractsMetaXmlMetadata()
    {
        $meta = (new OdtExtractor())->extract(Samples::path('tika-sample.odt'))->metadata;
        $this->assertSame('en-US', $meta['Language']);
        $this->assertArrayHasKey('Created', $meta);
        $this->assertStringContainsString('OpenOffice', $meta['Producer']);
    }

    public function testMissingContentRecordsTextErrorButKeepsMetadata()
    {
        // a real ODT with content.xml removed: meta.xml is a separate part, so
        // text extraction fails (recorded) while the metadata is still salvaged.
        $broken = Samples::withoutPart('tika-sample.odt', 'content.xml');
        $result = (new OdtExtractor())->extract($broken);
        $this->assertInstanceOf(ExtractionException::class, $result->textError);
        $this->assertSame('', $result->text);
        $this->assertNull($result->metadataError);
        $this->assertSame('en-US', $result->metadata['Language']);
    }
}
