<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\PdfExtractor;
use DokuWikiTest;

/**
 * Tests for the PDF extractor.
 *
 * @group plugin_totext
 */
class PdfExtractorTest extends DokuWikiTest
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
        $this->fixture = $this->tmp . '/sample.pdf';
        FixtureBuilder::buildPdf($this->fixture, 'Hello PDF world');
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        FixtureBuilder::cleanup($this->tmp);
        parent::tearDown();
    }

    public function testExtractsText()
    {
        $text = (new PdfExtractor())->extract($this->fixture);
        $this->assertStringContainsString('Hello PDF world', $text);
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new PdfExtractor())->extract($this->tmp . '/nonexistent.pdf');
    }

    public function testExtractsMultiLineText()
    {
        $path = $this->tmp . '/multiline.pdf';
        FixtureBuilder::buildPdf($path, 'Second sample line');
        $text = (new PdfExtractor())->extract($path);
        $this->assertStringContainsString('Second sample line', $text);
    }

    /**
     * @return array<string, array{0: string, 1: bool}>
     */
    public function provideSupports(): array
    {
        return [
            'pdf' => ['foo.pdf', true],
            'uppercase' => ['foo.PDF', true],
            'docx' => ['foo.docx', false],
        ];
    }

    /**
     * @dataProvider provideSupports
     */
    public function testSupports(string $path, bool $expected)
    {
        $this->assertSame($expected, (new PdfExtractor())->supports($path));
    }
}
