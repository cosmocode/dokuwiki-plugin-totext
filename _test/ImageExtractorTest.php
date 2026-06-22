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
        // of the description is asserted here. Images carry no body text.
        $result = (new ImageExtractor())->extract(Samples::path('tika-meta.jpg'));
        $this->assertSame('', $result->text);
        $meta = $result->metadata;
        $this->assertStringContainsString('Bird site in north eastern', $meta['Description']);
        $this->assertSame('Some Tourist', $meta['Author']);
        $this->assertStringContainsString('grazelands', $meta['Keywords']);
        $this->assertSame('Nokia N78', $meta['Producer']);
    }

    public function testImageWithoutMetadataYieldsEmptyResult()
    {
        $result = (new ImageExtractor())->extract(Samples::path('tika-plain.jpg'));
        $this->assertSame('', $result->text);
        $this->assertSame([], $result->metadata);
    }

    public function testExtractsTiffExifMetadata()
    {
        if (!function_exists('exif_read_data')) {
            $this->markTestSkipped('exif extension required to read TIFF metadata');
        }
        // meta.tiff stores its descriptive text in the EXIF ImageDescription tag
        $meta = (new ImageExtractor())->extract(Samples::path('tika-meta.tiff'))->metadata;
        $this->assertStringContainsString('Licensed to the Apache Software Foundation', $meta['Description']);
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new ImageExtractor())->extract($this->tmp . '/nope.jpg');
    }
}
