<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Exception\UnsupportedFormatException;
use dokuwiki\plugin\totext\Extractor\DocxExtractor;
use dokuwiki\plugin\totext\Extractor\ExtractorFactory;
use dokuwiki\plugin\totext\Extractor\ExtractorInterface;
use dokuwiki\plugin\totext\Extractor\OdtExtractor;
use dokuwiki\plugin\totext\Extractor\PdfExtractor;
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
        $this->tmp = io_mktmpdir();
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        io_rmdir($this->tmp, true);
        parent::tearDown();
    }

    /**
     * forFile() lowercases the extension and ignores the directory part, so these
     * mixed-case names and full paths must still route to the right extractor.
     *
     * The plain extension => class mappings live in EXTRACTORS and are exercised
     * via testEveryAdvertisedExtensionRoutes; re-listing them here would only
     * duplicate that map, so this covers just the normalisation logic.
     *
     * @return array<string, array{0: string, 1: class-string}>
     */
    public function provideCaseAndPath(): array
    {
        return [
            'uppercase extension' => ['foo.DOCX', DocxExtractor::class],
            'uppercase name and extension' => ['FOO.PDF', PdfExtractor::class],
            'full path with dirs and spaces' => ['/var/data/My File.ODT', OdtExtractor::class],
        ];
    }

    /**
     * @dataProvider provideCaseAndPath
     */
    public function testForFileNormalisesCaseAndPath(string $path, string $expected)
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
            'docx' => ['tika-sample.docx', 'Sample Word Document Title'],
            'xlsx' => ['tika-sample.xlsx', 'Number'],
            'pptx' => ['tika-sample.pptx', 'Rajiv'],
            'pdf' => ['tika-sample.pdf', 'Apache Tika is a toolkit'],
            'odt' => ['tika-sample.odt', 'sample Open Office document'],
            'ods' => ['tika-sample.ods', 'example'],
            'odp' => ['tika-sample.odp', 'An example Impress file'],
            'txt' => ['tika-sample.txt', 'quick brown fox'],
        ];
    }

    /**
     * @dataProvider provideRoundtrip
     */
    public function testExtractConvenienceRoundtripsFormat(string $file, string $needle)
    {
        $this->assertStringContainsString($needle, ExtractorFactory::extract(Samples::path($file))->text);
    }

    public function testCorruptDocxRaisesExtractionException()
    {
        $this->expectException(ExtractionException::class);
        ExtractorFactory::extract(Samples::corrupt('docx'));
    }

    public function testMissingFileRaisesExtractionException()
    {
        $this->expectException(ExtractionException::class);
        ExtractorFactory::extract($this->tmp . '/nope.docx');
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
