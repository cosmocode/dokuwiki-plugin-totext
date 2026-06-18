<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\ImageExtractor;
use DokuWikiTest;

/**
 * Tests for the image metadata extractor, run against real photos carrying real
 * IPTC/EXIF metadata, taken from the Apache Tika corpus (see data/README.md).
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
        $this->tmp = io_mktmpdir();
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        io_rmdir($this->tmp, true);
        parent::tearDown();
    }

    public function testExtractsJpegIptcAndExifMetadata()
    {
        // meta.jpg carries Photoshop IPTC (caption, by-line, keywords) plus EXIF
        // (camera). The IPTC was written non-UTF-8, so only the ASCII-safe part
        // of the caption is asserted here.
        $text = (new ImageExtractor())->extract(Samples::path('tika-meta.jpg'));
        $this->assertStringContainsString('Caption: Bird site in north eastern', $text);
        $this->assertStringContainsString('Author: Some Tourist', $text);
        $this->assertStringContainsString('Keywords: grazelands', $text);
        $this->assertStringContainsString('Camera: Nokia N78', $text);
    }

    public function testImageWithoutMetadataReturnsEmptyString()
    {
        $text = (new ImageExtractor())->extract(Samples::path('tika-plain.jpg'));
        $this->assertSame('', $text);
    }

    public function testExtractsTiffExifMetadata()
    {
        if (!function_exists('exif_read_data')) {
            $this->markTestSkipped('exif extension required to read TIFF metadata');
        }
        // meta.tiff stores its descriptive text in the EXIF ImageDescription tag
        $text = (new ImageExtractor())->extract(Samples::path('tika-meta.tiff'));
        $this->assertStringContainsString('Caption: Licensed to the Apache Software Foundation', $text);
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new ImageExtractor())->extract($this->tmp . '/nope.jpg');
    }
}
