<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Exception\UnsupportedFormatException;
use dokuwiki\plugin\totext\Extractor\DocxExtractor;
use dokuwiki\plugin\totext\Extractor\ExtractorFactory;
use dokuwiki\plugin\totext\Extractor\ExtractorInterface;
use dokuwiki\plugin\totext\Extractor\ImageExtractor;
use dokuwiki\plugin\totext\Extractor\OdpExtractor;
use dokuwiki\plugin\totext\Extractor\OdsExtractor;
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
        $this->tmp = Samples::tempDir();
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        Samples::cleanup($this->tmp);
        parent::tearDown();
    }

    /**
     * Extension => expected extractor class.
     *
     * @return array<string, array{0: string, 1: class-string}>
     */
    public function provideRouting(): array
    {
        return [
            'docx' => ['foo.docx', DocxExtractor::class],
            'xlsx' => ['foo.xlsx', XlsxExtractor::class],
            'pptx' => ['foo.pptx', PptxExtractor::class],
            'pdf' => ['foo.pdf', PdfExtractor::class],
            'odt' => ['foo.odt', OdtExtractor::class],
            'ods' => ['foo.ods', OdsExtractor::class],
            'odp' => ['foo.odp', OdpExtractor::class],
            'txt' => ['foo.txt', TextExtractor::class],
            'md' => ['foo.md', TextExtractor::class],
            'markdown' => ['foo.markdown', TextExtractor::class],
            'log' => ['foo.log', TextExtractor::class],
            'jpg' => ['foo.jpg', ImageExtractor::class],
            'jpeg' => ['foo.jpeg', ImageExtractor::class],
            'tiff' => ['foo.tiff', ImageExtractor::class],
            'uppercase docx' => ['foo.DOCX', DocxExtractor::class],
            'uppercase pdf' => ['FOO.PDF', PdfExtractor::class],
            'path with dirs' => ['/var/data/My File.ODT', OdtExtractor::class],
        ];
    }

    /**
     * @dataProvider provideRouting
     */
    public function testForFileRoutesByExtension(string $path, string $expected)
    {
        $this->assertInstanceOf($expected, ExtractorFactory::forFile($path));
    }

    /**
     * Names that must be rejected as unsupported.
     *
     * @return array<string, array{0: string}>
     */
    public function provideUnsupported(): array
    {
        return [
            'unknown extension' => ['foo.unknownext'],
            'no extension' => ['/tmp/somefile'],
            'legacy doc' => ['legacy.doc'],
            'legacy xls' => ['legacy.xls'],
            'legacy ppt' => ['legacy.ppt'],
            'png image' => ['foo.png'],
        ];
    }

    /**
     * @dataProvider provideUnsupported
     */
    public function testUnsupportedExtensionThrows(string $path)
    {
        $this->expectException(UnsupportedFormatException::class);
        ExtractorFactory::forFile($path);
    }

    /**
     * Every advertised extension must route to a usable extractor.
     *
     * supportedExtensions() and forFile() both read the factory's EXTRACTORS map,
     * so this confirms every advertised extension instantiates without error.
     *
     * @return array<string, array{0: string}>
     */
    public function provideSupportedExtensions(): array
    {
        $out = [];
        foreach (ExtractorFactory::supportedExtensions() as $ext) {
            $out[$ext] = [$ext];
        }
        return $out;
    }

    /**
     * @dataProvider provideSupportedExtensions
     */
    public function testEveryAdvertisedExtensionRoutes(string $ext)
    {
        $this->assertInstanceOf(ExtractorInterface::class, ExtractorFactory::forFile('file.' . $ext));
    }

    /**
     * Convenience extract() must route to the right extractor and return text
     * for every format, checked against the real sample files.
     *
     * @return array<string, array{0: string, 1: string}> [sample file, expected substring]
     */
    public function provideRoundtrip(): array
    {
        return [
            'docx' => ['sample.docx', 'Totext Sample Document'],
            'xlsx' => ['sample.xlsx', 'Widget'],
            'pptx' => ['sample.pptx', 'Slide One Title'],
            'pdf' => ['sample.pdf', 'Totext Sample Document'],
            'odt' => ['sample.odt', 'Totext Sample Document'],
            'ods' => ['sample.ods', 'Widget'],
            'odp' => ['sample.odp', 'Slide One Title'],
            'txt' => ['sample.txt', 'Plain text sample'],
        ];
    }

    /**
     * @dataProvider provideRoundtrip
     */
    public function testExtractConvenienceRoundtripsFormat(string $file, string $needle)
    {
        $this->assertStringContainsString($needle, ExtractorFactory::extract(Samples::path($file)));
    }

    public function testCorruptDocxRaisesExtractionException()
    {
        $this->expectException(ExtractionException::class);
        ExtractorFactory::extract(Samples::corrupt($this->tmp . '/bad.docx'));
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
