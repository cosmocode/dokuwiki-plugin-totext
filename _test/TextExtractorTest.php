<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\TextExtractor;
use DokuWikiTest;

/**
 * Tests for the plain-text family extractor.
 *
 * @group plugin_totext
 */
class TextExtractorTest extends DokuWikiTest
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

    public function testReadsPlainText()
    {
        $path = $this->tmp . '/sample.txt';
        FixtureBuilder::buildTextFile($path, "Hello text\nsecond line");
        $text = (new TextExtractor())->extract($path);
        $this->assertSame("Hello text\nsecond line", $text);
    }

    public function testNormalisesLineEndings()
    {
        $path = $this->tmp . '/crlf.txt';
        FixtureBuilder::buildTextFile($path, "one\r\ntwo\rthree");
        $text = (new TextExtractor())->extract($path);
        $this->assertSame("one\ntwo\nthree", $text);
    }

    public function testConvertsLatin1ToUtf8()
    {
        $path = $this->tmp . '/latin1.txt';
        // 0xE4 is "ä" in Latin-1, invalid as standalone UTF-8
        FixtureBuilder::buildTextFile($path, "caf\xE9");
        $text = (new TextExtractor())->extract($path);
        $this->assertSame('café', $text);
    }

    public function testReplacesInvalidBytes()
    {
        $path = $this->tmp . '/bad.txt';
        // a lone 0xFF byte is invalid in both UTF-8 and (after Latin-1 conversion)
        // must not survive as a raw byte in the output
        FixtureBuilder::buildTextFile($path, "ok\xFFok");
        $text = (new TextExtractor())->extract($path);
        $this->assertStringContainsString('ok', $text);
        $this->assertStringNotContainsString("\xFF", $text);
    }

    public function testEmptyFileYieldsEmptyString()
    {
        $path = $this->tmp . '/empty.txt';
        FixtureBuilder::buildTextFile($path, '');
        $this->assertSame('', (new TextExtractor())->extract($path));
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new TextExtractor())->extract($this->tmp . '/nope.txt');
    }

    /**
     * @return array<string, array{0: string, 1: bool}>
     */
    public function provideSupports(): array
    {
        return [
            'txt' => ['foo.txt', true],
            'md' => ['foo.md', true],
            'markdown' => ['foo.markdown', true],
            'csv' => ['foo.csv', true],
            'log' => ['foo.log', true],
            'text' => ['foo.text', true],
            'uppercase' => ['foo.TXT', true],
            'pdf' => ['foo.pdf', false],
            'docx' => ['foo.docx', false],
        ];
    }

    /**
     * @dataProvider provideSupports
     */
    public function testSupports(string $path, bool $expected)
    {
        $this->assertSame($expected, (new TextExtractor())->supports($path));
    }
}
