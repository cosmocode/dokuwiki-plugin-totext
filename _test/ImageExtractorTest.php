<?php

namespace dokuwiki\plugin\totext\test;

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
    }

    public function testEmitsLabelledLines()
    {
        $path = $this->tmp . '/sample.jpg';
        FixtureBuilder::buildJpeg($path);
        $text = (new ImageExtractor())->extract($path);
        $this->assertStringContainsString('Title: Test Title', $text);
        $this->assertStringContainsString('Author: Jane Photographer', $text);
    }

    public function testImageWithoutMetadataReturnsEmptyString()
    {
        $path = $this->tmp . '/plain.jpg';
        $img = imagecreatetruecolor(8, 8);
        imagejpeg($img, $path);

        $text = (new ImageExtractor())->extract($path);
        $this->assertSame('', $text);
    }

    public function testSupports()
    {
        $e = new ImageExtractor();
        $this->assertTrue($e->supports('foo.jpg'));
        $this->assertTrue($e->supports('foo.jpeg'));
        $this->assertTrue($e->supports('foo.tiff'));
        $this->assertFalse($e->supports('foo.png'));
    }
}
