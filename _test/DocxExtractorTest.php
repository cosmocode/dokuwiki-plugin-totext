<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\DocxExtractor;
use DokuWikiTest;

/**
 * Tests for the DOCX extractor, run against real LibreOffice output.
 *
 * @group plugin_totext
 */
class DocxExtractorTest extends DokuWikiTest
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

    public function testExtractsBodyHeadingTabAndLineBreak()
    {
        $text = (new DocxExtractor())->extract(Samples::path('formatting.docx'));
        $this->assertStringContainsString('Heading One', $text);
        $this->assertStringContainsString('Body paragraph text', $text);
        // <w:tab/> becomes a tab, <w:br/> becomes a newline
        $this->assertStringContainsString("Tab\tseparated", $text);
        $this->assertStringContainsString("Line one\nline two", $text);
    }

    public function testExtractsHeaderAndFooterText()
    {
        // header/footer live in word/header*.xml / word/footer*.xml, which the
        // extractor scans in addition to word/document.xml
        $text = (new DocxExtractor())->extract(Samples::path('formatting.docx'));
        $this->assertStringContainsString('Document header text', $text);
        $this->assertStringContainsString('Page footer text', $text);
    }

    public function testMissingDocumentPartThrows()
    {
        // a real DOCX with its main part removed
        $broken = Samples::withoutPart('sample.docx', 'word/document.xml', $this->tmp);
        $this->expectException(ExtractionException::class);
        (new DocxExtractor())->extract($broken);
    }

    public function testCorruptContainerThrows()
    {
        $this->expectException(ExtractionException::class);
        (new DocxExtractor())->extract(Samples::corrupt($this->tmp . '/corrupt.docx'));
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new DocxExtractor())->extract($this->tmp . '/nope.docx');
    }
}
