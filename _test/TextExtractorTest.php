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

    public function testMissingFileThrows()
    {
        $this->expectException(ExtractionException::class);
        (new TextExtractor())->extract($this->tmp . '/nope.txt');
    }

    public function testSupports()
    {
        $e = new TextExtractor();
        $this->assertTrue($e->supports('foo.txt'));
        $this->assertTrue($e->supports('foo.md'));
        $this->assertTrue($e->supports('foo.csv'));
        $this->assertFalse($e->supports('foo.pdf'));
    }
}
