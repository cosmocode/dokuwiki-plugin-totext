<?php

namespace dokuwiki\plugin\totext\Extractor;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use PrinsFrank\PdfParser\PdfParser;

/**
 * Extracts text from PDF documents using the bundled prinsfrank/pdfparser.
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
            // In-memory parsing ($useInMemoryStream = true, the default) is
            // both faster and far lighter than the previous smalot-based
            // parser, so there is no need for the slower file-handle mode.
            $document = (new PdfParser())->parseFile($path);
            return trim($document->getText());
        } catch (\Throwable $e) {
            throw new ExtractionException(
                "Failed to extract text from $path: " . $e->getMessage(),
                0,
                $e,
            );
        }
    }
}
