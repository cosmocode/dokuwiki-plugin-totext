<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\PptxExtractor;
use DokuWikiTest;

/**
 * Tests for the PPTX extractor, run against real LibreOffice output.
 *
 * @group plugin_totext
 */
class PptxExtractorTest extends DokuWikiTest
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

    public function testExtractsSlidesInOrderWithHeaders()
    {
        $text = (new PptxExtractor())->extract(Samples::path('sample.pptx'));
        $this->assertStringContainsString('=== Slide 1 ===', $text);
        $this->assertStringContainsString('=== Slide 2 ===', $text);
        $this->assertLessThan(
            strpos($text, 'Slide Two Title'),
            strpos($text, 'Slide One Title'),
        );
    }

    public function testExtractsSpeakerNotes()
    {
        $text = (new PptxExtractor())->extract(Samples::path('notes.pptx'));
        $this->assertStringContainsString('Visible slide body', $text);
        $this->assertStringContainsString('--- Notes ---', $text);
        $this->assertStringContainsString('These are the speaker notes.', $text);
        // notes follow the slide they belong to
        $this->assertLessThan(
            strpos($text, 'These are the speaker notes.'),
            strpos($text, 'Visible slide body'),
        );
    }

    public function testFallsBackToSlideFilesWhenPresentationMissing()
    {
        // a real PPTX whose ppt/presentation.xml is gone: with no sldIdLst the
        // extractor falls back to scanning the slide parts directly
        $broken = Samples::withoutPart('sample.pptx', 'ppt/presentation.xml', $this->tmp);
        $text = (new PptxExtractor())->extract($broken);
        $this->assertStringContainsString('Slide One Title', $text);
    }

    public function testCorruptContainerThrows()
    {
        $this->expectException(ExtractionException::class);
        (new PptxExtractor())->extract(Samples::corrupt($this->tmp . '/corrupt.pptx'));
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new PptxExtractor())->extract($this->tmp . '/nope.pptx');
    }
}
