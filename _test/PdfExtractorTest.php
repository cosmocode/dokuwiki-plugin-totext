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
        $text = (new PdfExtractor())->extract(Samples::path('tika-sample.pdf'));
        $this->assertStringContainsString('Tika - Content Analysis Toolkit', $text);
        $this->assertStringContainsString('Apache Tika is a toolkit', $text);
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
