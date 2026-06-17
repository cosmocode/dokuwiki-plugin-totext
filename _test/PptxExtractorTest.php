<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\PptxExtractor;
use DokuWikiTest;

/**
 * Tests for the PPTX extractor.
 *
 * @group plugin_totext
 */
class PptxExtractorTest extends DokuWikiTest
{
    /** @var string[] */
    protected $pluginsEnabled = ['totext'];

    /** @var string temp working directory */
    private $tmp = '';

    /** @var string fixture path */
    private $fixture = '';

    /** @inheritDoc */
    public function setUp(): void
    {
        parent::setUp();
        $this->tmp = FixtureBuilder::tempDir();
        $this->fixture = $this->tmp . '/sample.pptx';
        FixtureBuilder::buildPptx($this->fixture);
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        FixtureBuilder::cleanup($this->tmp);
        parent::tearDown();
    }

    public function testExtractsBothSlides()
    {
        $text = (new PptxExtractor())->extract($this->fixture);
        $this->assertStringContainsString('First slide title', $text);
        $this->assertStringContainsString('Second slide', $text);
    }

    public function testHonoursSldIdLstOrder()
    {
        // Fixture's sldIdLst points rId2 (slide1.xml = "First slide title") FIRST,
        // then rId1 (slide2.xml = "Second slide"). Display order must reflect that.
        $text = (new PptxExtractor())->extract($this->fixture);
        $posFirst = strpos($text, 'First slide title');
        $posSecond = strpos($text, 'Second slide');
        $this->assertNotFalse($posFirst);
        $this->assertNotFalse($posSecond);
        $this->assertLessThan($posSecond, $posFirst);
    }

    public function testSlideHeaders()
    {
        $text = (new PptxExtractor())->extract($this->fixture);
        $this->assertStringContainsString('=== Slide 1 ===', $text);
        $this->assertStringContainsString('=== Slide 2 ===', $text);
    }

    public function testExtractsSpeakerNotes()
    {
        $path = $this->tmp . '/notes.pptx';
        FixtureBuilder::buildPptxWithNotes($path);
        $text = (new PptxExtractor())->extract($path);
        $this->assertStringContainsString('Visible slide body', $text);
        $this->assertStringContainsString('--- Notes ---', $text);
        $this->assertStringContainsString('These are the speaker notes.', $text);
        // notes follow the slide they belong to
        $this->assertLessThan(
            strpos($text, 'These are the speaker notes.'),
            strpos($text, 'Visible slide body'),
        );
    }

    public function testNoSlidesThrows()
    {
        // a valid ZIP with neither presentation parts nor slide parts
        $path = $this->tmp . '/empty.pptx';
        FixtureBuilder::zip($path, ['[Content_Types].xml' => '<Types/>']);
        $this->expectException(ExtractionException::class);
        (new PptxExtractor())->extract($path);
    }

    /**
     * @return array<string, array{0: string, 1: bool}>
     */
    public function provideSupports(): array
    {
        return [
            'pptx' => ['foo.pptx', true],
            'uppercase' => ['foo.PPTX', true],
            'xlsx' => ['foo.xlsx', false],
            'legacy ppt' => ['foo.ppt', false],
        ];
    }

    /**
     * @dataProvider provideSupports
     */
    public function testSupports(string $path, bool $expected)
    {
        $this->assertSame($expected, (new PptxExtractor())->supports($path));
    }
}
