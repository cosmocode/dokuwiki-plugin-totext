<?php

namespace dokuwiki\plugin\totext\Extractor;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use Smalot\PdfParser\Parser;

/**
 * Extracts text from PDF documents using the bundled smalot/pdfparser.
 */
class PdfExtractor implements ExtractorInterface
{
    /** @inheritDoc */
    public function extract(string $path): string
    {
        if (!class_exists(Parser::class)) {
            throw new ExtractionException('PDF support is not installed (run composer install)');
        }
        if (!is_file($path)) {
            throw new ExtractionException("File not found: $path");
        }
        try {
            $pdf = (new Parser())->parseFile($path);
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
