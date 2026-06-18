<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\DocxExtractor;
use DokuWikiTest;

/**
 * Tests for the DOCX extractor, run against real Apache Tika sample documents.
 *
 * @group plugin_totext
 */
class DocxExtractorTest extends DokuWikiTest
{
    /** @var string[] */
    protected $pluginsEnabled = ['totext'];

    public function testExtractsHeadingsBodyAndTables()
    {
        $text = (new DocxExtractor())->extract(Samples::path('tika-sample.docx'));
        $this->assertStringContainsString('Sample Word Document Title', $text);
        $this->assertStringContainsString('Heading Level 1', $text);
        $this->assertStringContainsString('This is a sample Microsoft Word Document.', $text);
        // body table, including a nested table
        $this->assertStringContainsString('This is a table', $text);
        $this->assertStringContainsString('Nested table', $text);
    }

    public function testExtractsHeaderAndFooterText()
    {
        // header/footer live in word/header*.xml / word/footer*.xml, which the
        // extractor scans in addition to word/document.xml
        $text = (new DocxExtractor())->extract(Samples::path('tika-sample.docx'));
        $this->assertStringContainsString('This is the header for our document', $text);
        $this->assertStringContainsString('This is the footer for our document', $text);
    }

    public function testExtractsListsAndFootnotes()
    {
        $text = (new DocxExtractor())->extract(Samples::path('tika-various.docx'));
        $this->assertStringContainsString('Bullet 1', $text);
        $this->assertStringContainsString('Number bullet 1', $text);
        $this->assertStringContainsString('Footnote appears here', $text);
    }

    public function testPreservesMultibyteUtf8()
    {
        // tika-various.docx mixes Japanese and four-byte Gothic; both must survive
        $text = (new DocxExtractor())->extract(Samples::path('tika-various.docx'));
        $this->assertStringContainsString('ゾルゲと尾崎', $text, 'Japanese text lost');
        $this->assertStringContainsString('𐌲𐌿𐍄𐌹𐍃𐌺', $text, 'four-byte Gothic text lost');
    }

    public function testMissingDocumentPartThrows()
    {
        // a real DOCX with its main part removed
        $broken = Samples::withoutPart('tika-sample.docx', 'word/document.xml');
        $this->expectException(ExtractionException::class);
        (new DocxExtractor())->extract($broken);
    }
}
