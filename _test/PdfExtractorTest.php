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
        $this->tmp = Samples::tempDir();
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        Samples::cleanup($this->tmp);
        parent::tearDown();
    }

    public function testExtractsText()
    {
        $text = (new PdfExtractor())->extract(Samples::path('sample.pdf'));
        $this->assertStringContainsString('Totext Sample Document', $text);
        $this->assertStringContainsString('The quick brown fox jumps over the lazy dog.', $text);
    }

    public function testCorruptFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new PdfExtractor())->extract(Samples::corrupt($this->tmp . '/corrupt.pdf'));
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new PdfExtractor())->extract($this->tmp . '/nonexistent.pdf');
    }
}
