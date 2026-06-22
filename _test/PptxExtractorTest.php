<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Extractor\PptxExtractor;
use DokuWikiTest;

/**
 * Tests for the PPTX extractor, run against real Apache Tika sample decks.
 *
 * @group plugin_totext
 */
class PptxExtractorTest extends DokuWikiTest
{
    /** @var string[] */
    protected $pluginsEnabled = ['totext'];

    public function testExtractsSlidesInOrderWithHeaders()
    {
        $text = (new PptxExtractor())->extract(Samples::path('tika-sample.pptx'))->text;
        $this->assertStringContainsString('=== Slide 1 ===', $text);
        $this->assertStringContainsString('=== Slide 2 ===', $text);
        $this->assertStringContainsString('=== Slide 3 ===', $text);
        // content on the first slide must precede content on a later slide
        $this->assertLessThan(
            strpos($text, 'Different words to test against'),
            strpos($text, 'Rajiv'),
        );
    }

    public function testExtractsSpeakerNotes()
    {
        $text = (new PptxExtractor())->extract(Samples::path('tika-various.pptx'))->text;
        $this->assertStringContainsString('Here is a text box', $text);
        $this->assertStringContainsString('--- Notes ---', $text);
        // notes follow the slide body they belong to
        $this->assertLessThan(
            strpos($text, '--- Notes ---'),
            strpos($text, 'Here is a text box'),
        );
    }

    public function testExtractsCoreAndAppMetadata()
    {
        $meta = (new PptxExtractor())->extract(Samples::path('tika-sample.pptx'))->metadata;
        $this->assertSame('Attachment Test', $meta['Title']);
        $this->assertSame('Rajiv', $meta['Author']);
        $this->assertSame('Microsoft Office PowerPoint', $meta['Producer']);
    }

    public function testFallsBackToSlideFilesWhenPresentationMissing()
    {
        // a real PPTX whose ppt/presentation.xml is gone: with no sldIdLst the
        // extractor falls back to scanning the slide parts directly
        $broken = Samples::withoutPart('tika-sample.pptx', 'ppt/presentation.xml');
        $text = (new PptxExtractor())->extract($broken)->text;
        $this->assertStringContainsString('Rajiv', $text);
    }
}
