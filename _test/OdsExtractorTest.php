<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Extractor\OdsExtractor;
use DokuWikiTest;

/**
 * Tests for the ODS extractor.
 *
 * @group plugin_totext
 */
class OdsExtractorTest extends DokuWikiTest
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
        $this->fixture = $this->tmp . '/sample.ods';
        FixtureBuilder::buildOds($this->fixture);
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        FixtureBuilder::cleanup($this->tmp);
        parent::tearDown();
    }

    public function testIncludesSheetHeader()
    {
        $text = (new OdsExtractor())->extract($this->fixture);
        $this->assertStringContainsString('=== Sheet: Data ===', $text);
    }

    public function testRendersTabSeparatedRows()
    {
        $text = (new OdsExtractor())->extract($this->fixture);
        $this->assertStringContainsString("Hello\tWorld", $text);
        $this->assertStringContainsString("42\tinline", $text);
    }

    public function testSupports()
    {
        $e = new OdsExtractor();
        $this->assertTrue($e->supports('foo.ods'));
        $this->assertFalse($e->supports('foo.xlsx'));
    }
}
