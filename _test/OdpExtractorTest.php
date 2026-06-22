<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\OdpExtractor;
use DokuWikiTest;

/**
 * Tests for the ODP extractor, run against a real Apache Tika sample deck.
 *
 * @group plugin_totext
 */
class OdpExtractorTest extends DokuWikiTest
{
    /** @var string[] */
    protected $pluginsEnabled = ['totext'];

    public function testExtractsSlidesInOrderWithHeaders()
    {
        $text = (new OdpExtractor())->extract(Samples::path('tika-sample.odp'))->text;
        $this->assertStringContainsString('=== Slide 1 ===', $text);
        $this->assertStringContainsString('=== Slide 2 ===', $text);
        $this->assertStringContainsString('An example Impress file', $text);
        $this->assertLessThan(
            strpos($text, '=== Slide 2 ==='),
            strpos($text, '=== Slide 1 ==='),
        );
    }

    public function testExtractsMetaXmlMetadata()
    {
        $meta = (new OdpExtractor())->extract(Samples::path('tika-sample.odp'))->metadata;
        $this->assertSame('Beehive', $meta['Title']);
        $this->assertStringContainsString('LibreOffice', $meta['Producer']);
    }

    public function testMissingContentRecordsTextErrorButKeepsMetadata()
    {
        // a real ODP with content.xml removed: meta.xml is a separate part, so
        // text extraction fails (recorded) while the metadata is still salvaged.
        $broken = Samples::withoutPart('tika-sample.odp', 'content.xml');
        $result = (new OdpExtractor())->extract($broken);
        $this->assertInstanceOf(ExtractionException::class, $result->textError);
        $this->assertSame('', $result->text);
        $this->assertNull($result->metadataError);
        $this->assertSame('Beehive', $result->metadata['Title']);
    }
}
