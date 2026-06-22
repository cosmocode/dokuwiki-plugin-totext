<?php

namespace dokuwiki\plugin\totext\Extractor;

/**
 * Contract for all format-specific text extractors.
 */
interface ExtractorInterface
{
    /**
     * Extract body text and metadata from the given file.
     *
     * @param string $path absolute path to the file
     * @return ExtractionResult the extracted body text and canonical metadata
     * @throws \dokuwiki\plugin\totext\Exception\ExtractionException on I/O or parse failure
     */
    public function extract(string $path): ExtractionResult;
}
