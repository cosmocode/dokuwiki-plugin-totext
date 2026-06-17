<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Extractor\DocxExtractor;
use DokuWikiTest;

/**
 * Tests for the DOCX extractor.
 *
 * @group plugin_totext
 */
class DocxExtractorTest extends DokuWikiTest
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
        $this->fixture = $this->tmp . '/sample.docx';
        FixtureBuilder::buildDocx($this->fixture);
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        FixtureBuilder::cleanup($this->tmp);
        parent::tearDown();
    }

    public function testExtractsParagraphText()
    {
        $text = (new DocxExtractor())->extract($this->fixture);
        $this->assertStringContainsString('Hello world from DOCX', $text);
        $this->assertStringContainsString('Tab', $text);
        $this->assertStringContainsString('separated', $text);
        $this->assertStringContainsString('line two', $text);
    }

    public function testTabAndBreakProduceWhitespace()
    {
        $text = (new DocxExtractor())->extract($this->fixture);
        $this->assertStringContainsString("Tab\tseparated", $text);
        $this->assertStringContainsString("Line one\nline two", $text);
    }

    public function testParagraphBoundariesProduceNewlines()
    {
        $text = (new DocxExtractor())->extract($this->fixture);
        $lines = explode("\n", $text);
        $this->assertGreaterThanOrEqual(3, count($lines));
        $this->assertSame('Hello world from DOCX', $lines[0]);
    }

    public function testSupports()
    {
        $e = new DocxExtractor();
        $this->assertTrue($e->supports('foo.docx'));
        $this->assertTrue($e->supports('foo.DOCX'));
        $this->assertFalse($e->supports('foo.pdf'));
    }
}
