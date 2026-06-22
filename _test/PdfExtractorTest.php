<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\PdfExtractor;
use DokuWikiTest;

/**
 * Tests for the PDF extractor, run against a real PDF.
 *
 * @group plugin_totext
 */
class PdfExtractorTest extends DokuWikiTest
{
    /** @var string[] */
    protected $pluginsEnabled = ['totext'];

    /** @var string temp working directory */
    private $tmp = '';

    /** @inheritDoc */
    public function setUp(): void
    {
        parent::setUp();
        $this->tmp = io_mktmpdir();
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        io_rmdir($this->tmp, true);
        parent::tearDown();
    }

    public function testExtractsText()
    {
        $text = (new PdfExtractor())->extract(Samples::path('tika-sample.pdf'))->text;
        $this->assertStringContainsString('Tika - Content Analysis Toolkit', $text);
        $this->assertStringContainsString('Apache Tika is a toolkit', $text);
    }

    public function testExtractsInfoDictionaryMetadata()
    {
        // The Info dictionary is read independently of getText(), so this passes
        // regardless of the Form-XObject body-text limitation in prinsfrank.
        $meta = (new PdfExtractor())->extract(Samples::path('tika-sample.pdf'))->metadata;
        $this->assertSame('Apache Tika - Apache Tika', $meta['Title']);
        // Author is stored as a UTF-16BE literal string; this asserts the
        // iconv UTF-16BE shim decoded the accented name correctly.
        $this->assertSame('Bertrand Delacrétaz', $meta['Author']);
        $this->assertArrayHasKey('Created', $meta);
        $this->assertStringContainsString('2007-09-15', $meta['Created']);
        $this->assertStringContainsString('Quartz', $meta['Producer']);
    }

    public function testCorruptFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new PdfExtractor())->extract(Samples::corrupt('pdf'));
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new PdfExtractor())->extract($this->tmp . '/nonexistent.pdf');
    }
}
