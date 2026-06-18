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
        $text = (new OdpExtractor())->extract(Samples::path('tika-sample.odp'));
        $this->assertStringContainsString('=== Slide 1 ===', $text);
        $this->assertStringContainsString('=== Slide 2 ===', $text);
        $this->assertStringContainsString('An example Impress file', $text);
        $this->assertLessThan(
            strpos($text, '=== Slide 2 ==='),
            strpos($text, '=== Slide 1 ==='),
        );
    }

    public function testMissingContentThrows()
    {
        // a real ODP with its content.xml removed
        $broken = Samples::withoutPart('tika-sample.odp', 'content.xml');
        $this->expectException(ExtractionException::class);
        (new OdpExtractor())->extract($broken);
    }
}
