<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Exception\UnsupportedFormatException;
use dokuwiki\plugin\totext\Extractor\DocxExtractor;
use dokuwiki\plugin\totext\Extractor\ExtractorFactory;
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
     * Every advertised extension must route to an extractor that accepts it.
     *
     * Guards against drift between supportedExtensions() and the forFile() match
     * arms, which are maintained as two separate hand-written lists.
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
    public function testEveryAdvertisedExtensionRoutesAndIsAccepted(string $ext)
    {
        $extractor = ExtractorFactory::forFile('file.' . $ext);
        $this->assertTrue(
            $extractor->supports('file.' . $ext),
            "$ext routes to " . get_class($extractor) . " but supports() rejects it",
        );
    }

    /**
     * Convenience extract() must round-trip every container format.
     *
     * @return array<string, array{0: string, 1: string}> [extension, expected substring]
     */
    public function provideRoundtrip(): array
    {
        return [
            'docx' => ['a.docx', 'Hello world from DOCX'],
            'xlsx' => ['a.xlsx', 'Hello'],
            'pptx' => ['a.pptx', 'First slide title'],
            'pdf' => ['a.pdf', 'Round trip PDF'],
            'odt' => ['a.odt', 'Hello world from ODT'],
            'ods' => ['a.ods', 'Hello'],
            'odp' => ['a.odp', 'First slide title'],
            'txt' => ['a.txt', 'Plain text file'],
        ];
    }

    /**
     * @dataProvider provideRoundtrip
     */
    public function testExtractConvenienceRoundtripsFormat(string $file, string $needle)
    {
        $this->assertStringContainsString($needle, ExtractorFactory::extract($this->tmp . '/' . $file));
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
