<?php

namespace dokuwiki\plugin\totext\Extractor;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\TextString\TextStringValue;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Object\Decorator\InformationDictionary;
use PrinsFrank\PdfParser\PdfParser;

/**
 * Extracts text and metadata from PDF documents using the bundled
 * prinsfrank/pdfparser.
 */
class PdfExtractor implements ExtractorInterface
{
    /** @inheritDoc */
    public function extract(string $path): ExtractionResult
    {
        if (!is_file($path)) {
            throw new ExtractionException("File not found: $path");
        }

        // Parsing the document is the total-failure gate: if the file cannot be
        // parsed at all, neither text nor metadata is recoverable and we throw.
        // In-memory parsing ($useInMemoryStream = true, the default) is both
        // faster and far lighter than the previous smalot-based parser, so
        // there is no need for the slower file-handle mode.
        try {
            $document = (new PdfParser())->parseFile($path);
        } catch (\Throwable $e) {
            throw ExtractionException::wrap($e, "Failed to open $path");
        }

        // Body text and Info-dictionary metadata come from the same parse but
        // are independent: each failure is recorded so a broken content stream
        // never costs us the metadata, and vice versa.
        $text = '';
        $textError = null;
        try {
            $text = $this->extractText($document);
        } catch (\Throwable $e) {
            $textError = ExtractionException::wrap($e, "Failed to extract text from $path");
        }

        $metadata = [];
        $metadataError = null;
        try {
            $metadata = $this->extractMetadata($document);
        } catch (\Throwable $e) {
            $metadataError = ExtractionException::wrap($e, "Failed to extract metadata from $path");
        }

        return new ExtractionResult($text, $metadata, $textError, $metadataError);
    }

    /**
     * Extract the body text from the parsed document.
     *
     * @param Document $document the already-parsed PDF
     * @return string the body text
     * @throws \Throwable on a text extraction failure
     */
    protected function extractText(Document $document): string
    {
        return trim($document->getText());
    }

    /**
     * Read the canonical metadata from the document's Info dictionary.
     *
     * Throws on a genuine read failure (recorded by the caller as
     * $metadataError); a document that simply has no Info dictionary is not a
     * failure and yields an empty map.
     *
     * @param Document $document the already-parsed PDF
     * @return array<string, string> canonical key => value map
     * @throws \Throwable on a read/parse failure of the Info dictionary
     */
    protected function extractMetadata(Document $document): array
    {
        $info = $document->getInformationDictionary();
        if ($info === null) {
            return [];
        }
        $raw = [
            'Title' => $info->getTitle(),
            'Author' => $info->getAuthor(),
            'Subject' => $this->infoText($info, DictionaryKey::SUBJECT),
            'Keywords' => $this->infoText($info, DictionaryKey::KEYWORDS),
            'Created' => $info->getCreationDate()?->format(DATE_ATOM),
            'Modified' => $info->getModificationDate()?->format(DATE_ATOM),
            // "what produced this file": the Producer, or the Creator app
            // when no Producer was recorded.
            'Producer' => $info->getProducer() ?? $info->getCreator(),
        ];

        $meta = [];
        foreach ($raw as $key => $value) {
            if ($value === null) {
                continue;
            }
            $value = $this->normalizePdfString($value);
            if ($value !== '') {
                $meta[$key] = $value;
            }
        }
        return $meta;
    }

    /**
     * Read an Info-dictionary text-string value the decorator does not expose
     * directly (Subject, Keywords).
     *
     * @param InformationDictionary $info the Info dictionary
     * @param DictionaryKey $key the entry to read
     * @return string|null the raw text, or null if absent
     */
    protected function infoText(InformationDictionary $info, DictionaryKey $key): ?string
    {
        return $info->getDictionary()
            ->getValueForKey($info->document, $key, TextStringValue::class)
            ?->getText();
    }

    /**
     * Normalise a raw Info-dictionary text string to trimmed UTF-8.
     *
     * Temporary shim: prinsfrank/pdfparser (≤ v3.1.0) does not decode UTF-16BE
     * text strings stored as PDF literal strings. Their bytes arrive expanded
     * one-codepoint-per-byte (mb_chr on the octal escapes), so a UTF-16BE
     * byte-order mark surfaces as the mojibake "þÿ" (0xC3 0xBE 0xC3 0xBF);
     * collapsing that back to ISO-8859-1 restores the raw 0xFE 0xFF BOM. A
     * genuinely raw BOM is decoded directly. Remove once decoding is fixed
     * upstream.
     *
     * @param string $v the raw value from the Info dictionary
     * @return string trimmed UTF-8
     */
    protected function normalizePdfString(string $v): string
    {
        if (str_starts_with($v, "\xC3\xBE\xC3\xBF")) {
            $v = mb_convert_encoding($v, 'ISO-8859-1', 'UTF-8');
        }
        if (str_starts_with($v, "\xFE\xFF")) {
            $v = iconv('UTF-16BE', 'UTF-8//IGNORE', substr($v, 2)) ?: $v;
        }
        return trim($v);
    }
}
