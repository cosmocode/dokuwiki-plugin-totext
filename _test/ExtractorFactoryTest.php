<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Exception\UnsupportedFormatException;
use dokuwiki\plugin\totext\Extractor\DocxExtractor;
use dokuwiki\plugin\totext\Extractor\ExtractorFactory;
use dokuwiki\plugin\totext\Extractor\ImageExtractor;
use dokuwiki\plugin\totext\Extractor\OdtExtractor;
use dokuwiki\plugin\totext\Extractor\PdfExtractor;
use dokuwiki\plugin\totext\Extractor\PptxExtractor;
use dokuwiki\plugin\totext\Extractor\TextExtractor;
use dokuwiki\plugin\totext\Extractor\XlsxExtractor;
use DokuWikiTest;

/**
 * Tests for the extension-based extractor routing.
 *
 * @group plugin_totext
 */
class ExtractorFactoryTest extends DokuWikiTest
{
    /** @var string[] */
    protected $pluginsEnabled = ['totext'];

    /** @var string temp working directory */
    private $tmp = '';

    /** @inheritDoc */
    public function setUp(): void
    {
        parent::setUp();
        $this->tmp = FixtureBuilder::tempDir();
        FixtureBuilder::buildDocx($this->tmp . '/a.docx');
        FixtureBuilder::buildXlsx($this->tmp . '/a.xlsx');
        FixtureBuilder::buildPptx($this->tmp . '/a.pptx');
        FixtureBuilder::buildPdf($this->tmp . '/a.pdf', 'Round trip PDF');
        FixtureBuilder::buildOdt($this->tmp . '/a.odt');
        FixtureBuilder::buildOds($this->tmp . '/a.ods');
        FixtureBuilder::buildOdp($this->tmp . '/a.odp');
        FixtureBuilder::buildTextFile($this->tmp . '/a.txt', "Plain text file\nsecond line");
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        FixtureBuilder::cleanup($this->tmp);
        parent::tearDown();
    }

    public function testForFileRoutesByExtension()
    {
        $this->assertInstanceOf(DocxExtractor::class, ExtractorFactory::forFile('foo.docx'));
        $this->assertInstanceOf(XlsxExtractor::class, ExtractorFactory::forFile('foo.xlsx'));
        $this->assertInstanceOf(PptxExtractor::class, ExtractorFactory::forFile('foo.pptx'));
        $this->assertInstanceOf(PdfExtractor::class, ExtractorFactory::forFile('foo.pdf'));
        $this->assertInstanceOf(OdtExtractor::class, ExtractorFactory::forFile('foo.odt'));
        $this->assertInstanceOf(TextExtractor::class, ExtractorFactory::forFile('foo.txt'));
        $this->assertInstanceOf(TextExtractor::class, ExtractorFactory::forFile('foo.md'));
        $this->assertInstanceOf(ImageExtractor::class, ExtractorFactory::forFile('foo.jpg'));
        $this->assertInstanceOf(ImageExtractor::class, ExtractorFactory::forFile('foo.tiff'));
    }

    public function testForFileIsCaseInsensitive()
    {
        $this->assertInstanceOf(DocxExtractor::class, ExtractorFactory::forFile('foo.DOCX'));
        $this->assertInstanceOf(PdfExtractor::class, ExtractorFactory::forFile('FOO.PDF'));
    }

    public function testUnknownExtensionThrows()
    {
        $this->expectException(UnsupportedFormatException::class);
        ExtractorFactory::forFile('foo.unknownext');
    }

    public function testNoExtensionThrows()
    {
        $this->expectException(UnsupportedFormatException::class);
        ExtractorFactory::forFile('/tmp/somefile');
    }

    public function testLegacyDocFormatIsUnsupported()
    {
        $this->expectException(UnsupportedFormatException::class);
        ExtractorFactory::forFile('legacy.doc');
    }

    public function testExtractConvenienceRoundtripsAllFormats()
    {
        $this->assertStringContainsString('Hello world from DOCX', ExtractorFactory::extract($this->tmp . '/a.docx'));
        $this->assertStringContainsString('Hello', ExtractorFactory::extract($this->tmp . '/a.xlsx'));
        $this->assertStringContainsString('First slide title', ExtractorFactory::extract($this->tmp . '/a.pptx'));
        $this->assertStringContainsString('Round trip PDF', ExtractorFactory::extract($this->tmp . '/a.pdf'));
        $this->assertStringContainsString('Hello world from ODT', ExtractorFactory::extract($this->tmp . '/a.odt'));
        $this->assertStringContainsString('Hello', ExtractorFactory::extract($this->tmp . '/a.ods'));
        $this->assertStringContainsString('First slide title', ExtractorFactory::extract($this->tmp . '/a.odp'));
        $this->assertStringContainsString('Plain text file', ExtractorFactory::extract($this->tmp . '/a.txt'));
    }

    public function testCorruptDocxRaisesExtractionException()
    {
        $bad = $this->tmp . '/bad.docx';
        file_put_contents($bad, 'this is not a zip');
        $this->expectException(ExtractionException::class);
        ExtractorFactory::extract($bad);
    }

    public function testSupportedExtensionsListsKnownFormats()
    {
        $exts = ExtractorFactory::supportedExtensions();
        $this->assertContains('docx', $exts);
        $this->assertContains('pdf', $exts);
        $this->assertContains('odt', $exts);
        $this->assertContains('jpg', $exts);
    }
}
