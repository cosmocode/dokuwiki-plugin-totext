<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\ImageExtractor;
use DokuWikiTest;

/**
 * Tests for the image metadata extractor, run against real images carrying
 * real IPTC/EXIF metadata (written by exiftool — see data/regenerate.sh).
 *
 * @group plugin_totext
 */
class ImageExtractorTest extends DokuWikiTest
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

    public function testExtractsJpegIptcMetadata()
    {
        $text = (new ImageExtractor())->extract(Samples::path('meta.jpg'));
        $this->assertStringContainsString('Title: Sample Image Title', $text);
        $this->assertStringContainsString('Caption: A descriptive caption', $text);
        $this->assertStringContainsString('Author: Jane Photographer', $text);
        $this->assertStringContainsString('Copyright: Copyright ACME', $text);
        $this->assertStringContainsString('alpha', $text);
        $this->assertStringContainsString('beta', $text);
    }

    public function testImageWithoutMetadataReturnsEmptyString()
    {
        $text = (new ImageExtractor())->extract(Samples::path('plain.jpg'));
        $this->assertSame('', $text);
    }

    public function testExtractsTiffExifMetadata()
    {
        if (!function_exists('exif_read_data')) {
            $this->markTestSkipped('exif extension required to read TIFF metadata');
        }
        $text = (new ImageExtractor())->extract(Samples::path('meta.tiff'));
        $this->assertStringContainsString('Caption: A TIFF caption', $text);
        $this->assertStringContainsString('Author: Tina Tiff', $text);
        $this->assertStringContainsString('Copyright: Copyright TIFFCorp', $text);
    }

    public function testDecodesWindowsXpUtf16Tag()
    {
        if (!function_exists('exif_read_data')) {
            $this->markTestSkipped('exif extension required to read TIFF metadata');
        }
        // XPTitle is stored as UTF-16LE; it must surface as a clean UTF-8 title
        $text = (new ImageExtractor())->extract(Samples::path('meta.tiff'));
        $this->assertStringContainsString('Title: XP Title', $text);
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new ImageExtractor())->extract($this->tmp . '/nope.jpg');
    }
}
