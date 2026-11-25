<?php

namespace dokuwiki\plugin\totext\test;

use dokuwiki\plugin\totext\Extractor\AbstractZipXmlExtractor;
use dokuwiki\plugin\totext\Extractor\DocxExtractor;
use dokuwiki\plugin\totext\Extractor\OdpExtractor;
use dokuwiki\plugin\totext\Extractor\OdsExtractor;
use dokuwiki\plugin\totext\Extractor\OdtExtractor;
use dokuwiki\plugin\totext\Extractor\PptxExtractor;
use dokuwiki\plugin\totext\Extractor\XlsxExtractor;
use DokuWikiTest;
use XMLReader;

/**
 * Tests for the text size limit of the ZIP based extractors.
 *
 * @group plugin_totext
 */
class TextLimitTest extends DokuWikiTest
{
    /** @var string[] */
    protected $pluginsEnabled = ['totext'];

    /** text limit in bytes used by the limited extractors */
    public const LIMIT = 50;

    /**
     * Sample files with a normal and a limited extractor for their format.
     *
     * @return array<string, array{string, AbstractZipXmlExtractor, AbstractZipXmlExtractor}>
     */
    public function provideExtractors(): array
    {
        return [
            'docx' => ['tika-sample.docx', new DocxExtractor(), new class extends DocxExtractor {
                protected const MAX_TEXT_SIZE = TextLimitTest::LIMIT;
            }],
            'xlsx' => ['tika-sample.xlsx', new XlsxExtractor(), new class extends XlsxExtractor {
                protected const MAX_TEXT_SIZE = TextLimitTest::LIMIT;
            }],
            'pptx' => ['tika-sample.pptx', new PptxExtractor(), new class extends PptxExtractor {
                protected const MAX_TEXT_SIZE = TextLimitTest::LIMIT;
            }],
            'odt' => ['tika-sample.odt', new OdtExtractor(), new class extends OdtExtractor {
                protected const MAX_TEXT_SIZE = TextLimitTest::LIMIT;
            }],
            'ods' => ['tika-sample.ods', new OdsExtractor(), new class extends OdsExtractor {
                protected const MAX_TEXT_SIZE = TextLimitTest::LIMIT;
            }],
            'odp' => ['tika-sample.odp', new OdpExtractor(), new class extends OdpExtractor {
                protected const MAX_TEXT_SIZE = TextLimitTest::LIMIT;
            }],
        ];
    }

    /**
     * @dataProvider provideExtractors
     */
    public function testTruncatesTextAtLimit(
        string $sample,
        AbstractZipXmlExtractor $extractor,
        AbstractZipXmlExtractor $limited
    ) {
        $full = $extractor->extract(Samples::path($sample))->text;
        $this->assertGreaterThan(self::LIMIT, strlen($full));

        $result = $limited->extract(Samples::path($sample));
        $this->assertNull($result->textError);
        $this->assertLessThanOrEqual(self::LIMIT, strlen($result->text));
        $this->assertNotSame('', $result->text);
        $this->assertStringStartsWith($result->text, $full);
    }

    public function testCutTextKeepsCharacterBoundary()
    {
        // "ä" is two bytes, a cut after 2 bytes would split it
        $cut = self::callInaccessibleMethod(new DocxExtractor(), 'cutText', ['aäb', 2]);
        $this->assertSame('a', $cut);
    }

    public function testReadElementTextReadsNestedTextOnce()
    {
        $reader = new XMLReader();
        $reader->XML('<r><t>a<t>b<t>c</t></t></t><x>d</x></r>');
        $extractor = new DocxExtractor();
        $texts = [];
        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 't') {
                $texts[] = self::callInaccessibleMethod($extractor, 'readElementText', [$reader]);
            }
        }
        $reader->close();
        $this->assertSame(['abc'], $texts);
    }
}
