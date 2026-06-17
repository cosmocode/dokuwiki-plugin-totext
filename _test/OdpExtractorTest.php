<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Extractor\OdpExtractor;
use DokuWikiTest;

/**
 * Tests for the ODP extractor.
 *
 * @group plugin_totext
 */
class OdpExtractorTest extends DokuWikiTest
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
        $this->fixture = $this->tmp . '/sample.odp';
        FixtureBuilder::buildOdp($this->fixture);
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        FixtureBuilder::cleanup($this->tmp);
        parent::tearDown();
    }

    public function testExtractsBothSlides()
    {
        $text = (new OdpExtractor())->extract($this->fixture);
        $this->assertStringContainsString('First slide title', $text);
        $this->assertStringContainsString('Second slide', $text);
    }

    public function testSlideHeadersInOrder()
    {
        $text = (new OdpExtractor())->extract($this->fixture);
        $this->assertStringContainsString('=== Slide 1 ===', $text);
        $this->assertStringContainsString('=== Slide 2 ===', $text);
        $this->assertLessThan(strpos($text, 'Second slide'), strpos($text, 'First slide title'));
    }

    public function testSupports()
    {
        $e = new OdpExtractor();
        $this->assertTrue($e->supports('foo.odp'));
        $this->assertFalse($e->supports('foo.pptx'));
    }
}
