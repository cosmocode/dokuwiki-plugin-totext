<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
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

    public function testExtractsHeaderAndFooterText()
    {
        $path = $this->tmp . '/headfoot.docx';
        FixtureBuilder::buildDocxWithHeaderFooter($path);
        $text = (new DocxExtractor())->extract($path);
        $this->assertStringContainsString('Body paragraph text', $text);
        $this->assertStringContainsString('Document header text', $text);
        $this->assertStringContainsString('Page footer text', $text);
    }

    public function testMissingDocumentPartThrows()
    {
        $path = $this->tmp . '/nodoc.docx';
        // a valid ZIP, but without word/document.xml
        FixtureBuilder::zip($path, ['[Content_Types].xml' => '<Types/>']);
        $this->expectException(ExtractionException::class);
        (new DocxExtractor())->extract($path);
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new DocxExtractor())->extract($this->tmp . '/nope.docx');
    }

    /**
     * @return array<string, array{0: string, 1: bool}>
     */
    public function provideSupports(): array
    {
        return [
            'docx' => ['foo.docx', true],
            'uppercase' => ['foo.DOCX', true],
            'pdf' => ['foo.pdf', false],
            'legacy doc' => ['foo.doc', false],
        ];
    }

    /**
     * @dataProvider provideSupports
     */
    public function testSupports(string $path, bool $expected)
    {
        $this->assertSame($expected, (new DocxExtractor())->supports($path));
    }
}
