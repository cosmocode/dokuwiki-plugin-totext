<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use DokuWikiTest;

/**
 * Tests for the helper component's convenience API — in particular the
 * throw-on-failure contract that single-output callers (e.g. the docsearch
 * plugin, which falls back to its own converters on failure) rely on.
 *
 * @group plugin_totext
 */
class HelperTest extends DokuWikiTest
{
    /** @var string[] */
    protected $pluginsEnabled = ['totext'];

    /**
     * Load the helper component.
     *
     * @return \helper_plugin_totext
     */
    private function helper(): \helper_plugin_totext
    {
        /** @var \helper_plugin_totext $totext */
        $totext = plugin_load('helper', 'totext');
        return $totext;
    }

    public function testExtractReturnsPartialResultWhenBodyPartMissing()
    {
        // text extraction fails but the metadata half is salvaged
        $broken = Samples::withoutPart('tika-sample.docx', 'word/document.xml');
        $result = $this->helper()->extract($broken);
        $this->assertFalse($result->isComplete());
        $this->assertInstanceOf(ExtractionException::class, $result->textError);
        $this->assertNull($result->metadataError);
        $this->assertSame('Sample Word Document', $result->metadata['Title']);
    }

    public function testExtractTextStillThrowsWhenBodyPartMissing()
    {
        // even though metadata was salvaged, extractText() must still throw so
        // callers relying on the throw-on-failure contract keep working
        $broken = Samples::withoutPart('tika-sample.docx', 'word/document.xml');
        $this->expectException(ExtractionException::class);
        $this->helper()->extractText($broken);
    }

    public function testExtractTextReturnsBodyOnSuccess()
    {
        $text = $this->helper()->extractText(Samples::path('tika-sample.docx'));
        $this->assertStringContainsString('Sample Word Document Title', $text);
    }

    public function testExtractMetadataReturnsMapOnSuccess()
    {
        $meta = $this->helper()->extractMetadata(Samples::path('tika-sample.docx'));
        $this->assertSame('Keith Bennett', $meta['Author']);
    }
}
