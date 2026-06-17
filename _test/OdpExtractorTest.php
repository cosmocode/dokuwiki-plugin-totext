<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\OdpExtractor;
use DokuWikiTest;

/**
 * Tests for the ODP extractor, run against real LibreOffice output.
 *
 * @group plugin_totext
 */
class OdpExtractorTest extends DokuWikiTest
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

    public function testExtractsSlidesInOrderWithHeaders()
    {
        $text = (new OdpExtractor())->extract(Samples::path('sample.odp'));
        $this->assertStringContainsString('=== Slide 1 ===', $text);
        $this->assertStringContainsString('=== Slide 2 ===', $text);
        $this->assertStringContainsString('Slide One Title', $text);
        $this->assertLessThan(
            strpos($text, 'Slide Two Title'),
            strpos($text, 'Slide One Title'),
        );
    }

    public function testMissingContentThrows()
    {
        // a real ODP with its content.xml removed
        $broken = Samples::withoutPart('sample.odp', 'content.xml', $this->tmp);
        $this->expectException(ExtractionException::class);
        (new OdpExtractor())->extract($broken);
    }

    public function testCorruptContainerThrows()
    {
        $this->expectException(ExtractionException::class);
        (new OdpExtractor())->extract(Samples::corrupt($this->tmp . '/corrupt.odp'));
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new OdpExtractor())->extract($this->tmp . '/nope.odp');
    }
}
