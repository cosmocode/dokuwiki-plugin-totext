<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Extractor\OdtExtractor;
use DokuWikiTest;

/**
 * Tests for the ODT extractor.
 *
 * @group plugin_totext
 */
class OdtExtractorTest extends DokuWikiTest
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
        $this->fixture = $this->tmp . '/sample.odt';
        FixtureBuilder::buildOdt($this->fixture);
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        FixtureBuilder::cleanup($this->tmp);
        parent::tearDown();
    }

    public function testExtractsText()
    {
        $text = (new OdtExtractor())->extract($this->fixture);
        $this->assertStringContainsString('Hello world from ODT', $text);
        $this->assertStringContainsString('First paragraph', $text);
        $this->assertStringContainsString('line two', $text);
    }

    public function testTabAndLineBreakProduceWhitespace()
    {
        $text = (new OdtExtractor())->extract($this->fixture);
        $this->assertStringContainsString("First paragraph\tafter tab", $text);
        $this->assertStringContainsString("Line one\nline two", $text);
    }

    public function testHeadingStartsOnOwnLine()
    {
        $text = (new OdtExtractor())->extract($this->fixture);
        $lines = explode("\n", $text);
        $this->assertSame('Hello world from ODT', $lines[0]);
    }

    public function testSupports()
    {
        $e = new OdtExtractor();
        $this->assertTrue($e->supports('foo.odt'));
        $this->assertFalse($e->supports('foo.docx'));
    }
}
