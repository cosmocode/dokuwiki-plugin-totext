<?php

namespace dokuwiki\plugin\totext\Extractor;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use Smalot\PdfParser\Config;
use Smalot\PdfParser\Parser;

/**
 * Extracts text from PDF documents using the bundled smalot/pdfparser.
 */
class PdfExtractor implements ExtractorInterface
{
    /** @inheritDoc */
    public function extract(string $path): string
    {
        if (!is_file($path)) {
            throw new ExtractionException("File not found: $path");
        }
        try {
            $config = new Config();
            // We only want the text. Not retaining image content makes the
            // parser skip decoding image streams outright — those are the
            // largest streams in a PDF, so this markedly lowers peak memory
            // (and never affects the extracted text).
            $config->setRetainImageContent(false);
            $pdf = (new Parser([], $config))->parseFile($path);
            return trim($pdf->getText());
        } catch (\Throwable $e) {
            throw new ExtractionException(
                "Failed to extract text from $path: " . $e->getMessage(),
                0,
                $e,
            );
        }
    }
}
