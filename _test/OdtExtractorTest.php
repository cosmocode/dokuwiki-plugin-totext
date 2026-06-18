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
        $text = (new OdtExtractor())->extract(Samples::path('tika-sample.odt'));
        $this->assertStringContainsString('This is a sample Open Office document', $text);
        $this->assertStringContainsString('NeoOffice', $text);
    }

    public function testMissingContentThrows()
    {
        // a real ODT with its content.xml removed
        $broken = Samples::withoutPart('tika-sample.odt', 'content.xml');
        $this->expectException(ExtractionException::class);
        (new OdtExtractor())->extract($broken);
    }
}
