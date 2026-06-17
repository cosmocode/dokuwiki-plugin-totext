<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Extractor\ExtractorFactory;
use DokuWikiTest;

/**
 * Tests extraction against real-world office files.
 *
 * Unlike the per-extractor tests, which build minimal hand-written fixtures,
 * these samples were produced by LibreOffice and therefore carry the full part
 * structure, styles and namespacing of genuine office documents. The files live
 * in _test/data and share the same source content across formats.
 *
 * @group plugin_totext
 */
class SampleFilesTest extends DokuWikiTest
{
    /** @var string[] */
    protected $pluginsEnabled = ['totext'];

    /** path to the committed sample files */
    private const DATA = __DIR__ . '/data';

    /**
     * Sample file => substrings that must appear in the extracted text.
     *
     * @return array<string, array{0: string, 1: string[]}>
     */
    public function provideSamples(): array
    {
        $prose = [
            'Totext Sample Document',
            'The quick brown fox jumps over the lazy dog.',
            'café',
            'naïve',
            '你好',
        ];
        return [
            'docx' => ['sample.docx', $prose],
            'odt' => ['sample.odt', $prose],
            'xlsx' => ['sample.xlsx', ['Widget', '42', 'Gadget', 'Name', 'Value']],
            'ods' => ['sample.ods', ['Widget', '42', 'Gadget', 'Name', 'Value']],
            'pptx' => ['sample.pptx', ['Slide One Title', 'First slide body text', 'Slide Two Title']],
            'odp' => ['sample.odp', ['Slide One Title', 'First slide body text', 'Slide Two Title']],
            // the PDF text layer mangles CJK/table layout, so assert prose only
            'pdf' => ['sample.pdf', ['Totext Sample Document', 'The quick brown fox jumps over the lazy dog.']],
        ];
    }

    /**
     * @dataProvider provideSamples
     */
    public function testExtractsExpectedContent(string $file, array $needles)
    {
        $path = self::DATA . '/' . $file;
        $this->assertFileExists($path, "missing sample fixture $file");
        $text = ExtractorFactory::extract($path);
        foreach ($needles as $needle) {
            $this->assertStringContainsString($needle, $text, "expected '$needle' in $file output");
        }
    }

    public function testSpreadsheetSamplesCarrySheetHeader()
    {
        foreach (['sample.xlsx', 'sample.ods'] as $file) {
            $text = ExtractorFactory::extract(self::DATA . '/' . $file);
            $this->assertStringContainsString('=== Sheet:', $text, "no sheet header in $file");
            $this->assertStringContainsString("Widget\t42", $text, "cells not tab-separated in $file");
        }
    }

    public function testPresentationSamplesKeepSlideOrder()
    {
        foreach (['sample.pptx', 'sample.odp'] as $file) {
            $text = ExtractorFactory::extract(self::DATA . '/' . $file);
            $this->assertStringContainsString('=== Slide 1 ===', $text);
            $this->assertStringContainsString('=== Slide 2 ===', $text);
            $this->assertLessThan(
                strpos($text, 'Slide Two Title'),
                strpos($text, 'Slide One Title'),
                "slide order wrong in $file",
            );
        }
    }
}
