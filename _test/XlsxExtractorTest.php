<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Extractor\XlsxExtractor;
use DokuWikiTest;

/**
 * Tests for the XLSX extractor.
 *
 * @group plugin_totext
 */
class XlsxExtractorTest extends DokuWikiTest
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
        $this->fixture = $this->tmp . '/sample.xlsx';
        FixtureBuilder::buildXlsx($this->fixture);
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        FixtureBuilder::cleanup($this->tmp);
        parent::tearDown();
    }

    public function testIncludesSheetHeader()
    {
        $text = (new XlsxExtractor())->extract($this->fixture);
        $this->assertStringContainsString('=== Sheet: Data ===', $text);
    }

    public function testResolvesSharedStrings()
    {
        $text = (new XlsxExtractor())->extract($this->fixture);
        $this->assertStringContainsString("Hello\tWorld", $text);
    }

    public function testIncludesRawAndInlineValues()
    {
        $text = (new XlsxExtractor())->extract($this->fixture);
        $this->assertStringContainsString("42\tinline", $text);
    }

    public function testSupports()
    {
        $e = new XlsxExtractor();
        $this->assertTrue($e->supports('foo.xlsx'));
        $this->assertFalse($e->supports('foo.docx'));
    }
}
