<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\UnsupportedFormatException;
use DokuWikiTest;

/**
 * Tests for the helper component end-to-end.
 *
 * @group plugin_totext
 */
class HelperTest extends DokuWikiTest
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
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        FixtureBuilder::cleanup($this->tmp);
        parent::tearDown();
    }

    public function testHelperLoads()
    {
        $helper = plugin_load('helper', 'totext');
        $this->assertInstanceOf(\helper_plugin_totext::class, $helper);
    }

    public function testExtractTextHappyPath()
    {
        $path = $this->tmp . '/sample.docx';
        FixtureBuilder::buildDocx($path);

        /** @var \helper_plugin_totext $helper */
        $helper = plugin_load('helper', 'totext');
        $text = $helper->extractText($path);
        $this->assertStringContainsString('Hello world from DOCX', $text);
    }

    public function testExtractTextThrowsOnUnsupported()
    {
        /** @var \helper_plugin_totext $helper */
        $helper = plugin_load('helper', 'totext');
        $this->expectException(UnsupportedFormatException::class);
        $helper->extractText($this->tmp . '/file.unknownext');
    }

    public function testIsSupported()
    {
        /** @var \helper_plugin_totext $helper */
        $helper = plugin_load('helper', 'totext');
        $this->assertTrue($helper->isSupported('foo.docx'));
        $this->assertTrue($helper->isSupported('foo.PDF'));
        $this->assertFalse($helper->isSupported('foo.unknownext'));
    }

    public function testSupportedExtensions()
    {
        /** @var \helper_plugin_totext $helper */
        $helper = plugin_load('helper', 'totext');
        $this->assertContains('odt', $helper->supportedExtensions());
    }
}
