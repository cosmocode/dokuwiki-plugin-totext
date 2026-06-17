<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\ImageExtractor;
use DokuWikiTest;

/**
 * Tests for the image metadata extractor.
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
        if (!function_exists('imagejpeg')) {
            $this->markTestSkipped('GD extension with JPEG support required to build fixtures');
        }
        $this->tmp = FixtureBuilder::tempDir();
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        FixtureBuilder::cleanup($this->tmp);
        parent::tearDown();
    }

    public function testExtractsIptcMetadata()
    {
        $path = $this->tmp . '/sample.jpg';
        FixtureBuilder::buildJpeg($path);
        $text = (new ImageExtractor())->extract($path);
        $this->assertStringContainsString('Test Title', $text);
        $this->assertStringContainsString('A caption describing the image', $text);
        $this->assertStringContainsString('Jane Photographer', $text);
        $this->assertStringContainsString('Copyright ACME', $text);
        $this->assertStringContainsString('alpha beta', $text);
    }

    public function testEmitsLabelledLines()
    {
        $path = $this->tmp . '/sample.jpg';
        FixtureBuilder::buildJpeg($path);
        $text = (new ImageExtractor())->extract($path);
        $this->assertStringContainsString('Title: Test Title', $text);
        $this->assertStringContainsString('Author: Jane Photographer', $text);
        $this->assertStringContainsString('Keywords: alpha beta', $text);
    }

    public function testImageWithoutMetadataReturnsEmptyString()
    {
        $path = $this->tmp . '/plain.jpg';
        $img = imagecreatetruecolor(8, 8);
        imagejpeg($img, $path);

        $text = (new ImageExtractor())->extract($path);
        $this->assertSame('', $text);
    }

    public function testExtractsTiffExifMetadata()
    {
        if (!function_exists('exif_read_data')) {
            $this->markTestSkipped('exif extension required to read TIFF metadata');
        }
        $path = $this->tmp . '/sample.tiff';
        FixtureBuilder::buildTiffWithExif($path);
        $text = (new ImageExtractor())->extract($path);
        $this->assertStringContainsString('Caption: A TIFF caption', $text);
        $this->assertStringContainsString('Author: Tina Tiff', $text);
        $this->assertStringContainsString('Copyright: Copyright TIFFCorp', $text);
    }

    public function testDecodesWindowsXpUtf16Tag()
    {
        if (!function_exists('exif_read_data')) {
            $this->markTestSkipped('exif extension required to read TIFF metadata');
        }
        $path = $this->tmp . '/sample.tiff';
        FixtureBuilder::buildTiffWithExif($path);
        $text = (new ImageExtractor())->extract($path);
        // XPTitle is stored as UTF-16LE; it must surface as a clean UTF-8 title
        $this->assertStringContainsString('Title: XP Title', $text);
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new ImageExtractor())->extract($this->tmp . '/nope.jpg');
    }
}
