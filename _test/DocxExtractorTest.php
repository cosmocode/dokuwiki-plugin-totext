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
        $text = (new DocxExtractor())->extract(Samples::path('tika-sample.docx'))->text;
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
        $text = (new DocxExtractor())->extract(Samples::path('tika-sample.docx'))->text;
        $this->assertStringContainsString('This is the header for our document', $text);
        $this->assertStringContainsString('This is the footer for our document', $text);
    }

    public function testExtractsListsAndFootnotes()
    {
        $text = (new DocxExtractor())->extract(Samples::path('tika-various.docx'))->text;
        $this->assertStringContainsString('Bullet 1', $text);
        $this->assertStringContainsString('Number bullet 1', $text);
        $this->assertStringContainsString('Footnote appears here', $text);
    }

    public function testPreservesMultibyteUtf8()
    {
        // tika-various.docx mixes Japanese and four-byte Gothic; both must survive
        $text = (new DocxExtractor())->extract(Samples::path('tika-various.docx'))->text;
        $this->assertStringContainsString('ゾルゲと尾崎', $text, 'Japanese text lost');
        $this->assertStringContainsString('𐌲𐌿𐍄𐌹𐍃𐌺', $text, 'four-byte Gothic text lost');
    }

    public function testExtractsCoreAndAppMetadata()
    {
        // docProps/core.xml (Dublin Core) + docProps/app.xml (Application)
        $meta = (new DocxExtractor())->extract(Samples::path('tika-sample.docx'))->metadata;
        $this->assertSame('Sample Word Document', $meta['Title']);
        $this->assertSame('Keith Bennett', $meta['Author']);
        $this->assertArrayHasKey('Created', $meta);
        $this->assertArrayHasKey('Modified', $meta);
        $this->assertSame('Microsoft Office Word', $meta['Producer']);
    }

    public function testDropsEmptyMetadataValues()
    {
        // tika-various.docx has empty <dc:title> and <dc:description> elements;
        // empty values must not surface as blank keys.
        $meta = (new DocxExtractor())->extract(Samples::path('tika-various.docx'))->metadata;
        $this->assertSame('Michael McCandless', $meta['Author']);
        $this->assertSame('Subject is here', $meta['Subject']);
        $this->assertSame('Keyword1 Keyword2', $meta['Keywords']);
        $this->assertArrayNotHasKey('Title', $meta);
        $this->assertArrayNotHasKey('Description', $meta);
    }

    public function testMissingDocumentPartRecordsTextErrorButKeepsMetadata()
    {
        // a real DOCX with its main body part removed: the container still opens,
        // so text extraction fails (recorded as textError) while the independent
        // docProps metadata is still salvaged.
        $broken = Samples::withoutPart('tika-sample.docx', 'word/document.xml');
        $result = (new DocxExtractor())->extract($broken);
        $this->assertInstanceOf(ExtractionException::class, $result->textError);
        $this->assertSame('', $result->text);
        $this->assertNull($result->metadataError);
        $this->assertSame('Sample Word Document', $result->metadata['Title']);
    }
}
