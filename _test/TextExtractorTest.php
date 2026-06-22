<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\plugin\totext\Extractor\TextExtractor;
use DokuWikiTest;

/**
 * Tests for the plain-text family extractor.
 *
 * Plain text needs no container, so these write the exact bytes under test
 * directly to disk — there is no fabricated structure to get wrong.
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
        $this->tmp = io_mktmpdir();
    }

    /** @inheritDoc */
    public function tearDown(): void
    {
        io_rmdir($this->tmp, true);
        parent::tearDown();
    }

    public function testReadsRealSampleFile()
    {
        // tika-sample.txt is a multilingual UTF-8 pangram; the multibyte text must survive
        $result = (new TextExtractor())->extract(Samples::path('tika-sample.txt'));
        $this->assertStringContainsString('The quick brown fox jumps over the lazy dog', $result->text);
        $this->assertStringContainsString('über', $result->text);
        $this->assertStringContainsString('براون', $result->text);
        // plain text carries no structured metadata
        $this->assertSame([], $result->metadata);
    }

    public function testReadsPlainText()
    {
        $path = $this->tmp . '/sample.txt';
        file_put_contents($path, "Hello text\nsecond line");
        $text = (new TextExtractor())->extract($path)->text;
        $this->assertSame("Hello text\nsecond line", $text);
    }

    public function testNormalisesLineEndings()
    {
        $path = $this->tmp . '/crlf.txt';
        file_put_contents($path, "one\r\ntwo\rthree");
        $text = (new TextExtractor())->extract($path)->text;
        $this->assertSame("one\ntwo\nthree", $text);
    }

    public function testConvertsLatin1ToUtf8()
    {
        $path = $this->tmp . '/latin1.txt';
        // 0xE9 is "é" in Latin-1, invalid as standalone UTF-8
        file_put_contents($path, "caf\xE9");
        $text = (new TextExtractor())->extract($path)->text;
        $this->assertSame('café', $text);
    }

    public function testReplacesInvalidBytes()
    {
        $path = $this->tmp . '/bad.txt';
        // a lone 0xFF byte must not survive as a raw byte in the output
        file_put_contents($path, "ok\xFFok");
        $text = (new TextExtractor())->extract($path)->text;
        $this->assertStringContainsString('ok', $text);
        $this->assertStringNotContainsString("\xFF", $text);
    }

    public function testEmptyFileYieldsEmptyString()
    {
        $path = $this->tmp . '/empty.txt';
        file_put_contents($path, '');
        $this->assertSame('', (new TextExtractor())->extract($path)->text);
    }

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new TextExtractor())->extract($this->tmp . '/nope.txt');
    }
}
