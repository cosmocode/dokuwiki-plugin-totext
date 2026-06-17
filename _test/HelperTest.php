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

    public function testHelperLoads()
    {
        $helper = plugin_load('helper', 'totext');
        $this->assertInstanceOf(\helper_plugin_totext::class, $helper);
    }

    public function testExtractTextHappyPath()
    {
        /** @var \helper_plugin_totext $helper */
        $helper = plugin_load('helper', 'totext');
        $text = $helper->extractText(Samples::path('sample.docx'));
        $this->assertStringContainsString('Totext Sample Document', $text);
    }

    public function testExtractTextThrowsOnUnsupported()
    {
        /** @var \helper_plugin_totext $helper */
        $helper = plugin_load('helper', 'totext');
        $this->expectException(UnsupportedFormatException::class);
        $helper->extractText('/tmp/file.unknownext');
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
