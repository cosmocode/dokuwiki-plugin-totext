<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\OdtExtractor;
use DokuWikiTest;

/**
 * Tests for the ODT extractor, run against real LibreOffice output.
 *
 * @group plugin_totext
 */
class OdtExtractorTest extends DokuWikiTest
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

    public function testExtractsHeadingTabAndLineBreak()
    {
        $text = (new OdtExtractor())->extract(Samples::path('formatting.odt'));
        $this->assertStringContainsString('Heading One', $text);
        $this->assertStringContainsString('Body paragraph text', $text);
        // <text:tab/> becomes a tab, <text:line-break/> becomes a newline
        $this->assertStringContainsString("Tab\tseparated", $text);
        $this->assertStringContainsString("Line one\nline two", $text);
    }

    public function testHeadingStartsOnOwnLine()
    {
        $text = (new OdtExtractor())->extract(Samples::path('formatting.odt'));
        $lines = explode("\n", $text);
        $this->assertSame('Heading One', $lines[0]);
    }

    public function testMissingContentThrows()
    {
        // a real ODT with its content.xml removed
        $broken = Samples::withoutPart('sample.odt', 'content.xml', $this->tmp);
        $this->expectException(ExtractionException::class);
        (new OdtExtractor())->extract($broken);
    }

    public function testCorruptContainerThrows()
    {
        $this->expectException(ExtractionException::class);
        (new OdtExtractor())->extract(Samples::corrupt($this->tmp . '/corrupt.odt'));
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new OdtExtractor())->extract($this->tmp . '/nope.odt');
    }
}
