<?php

namespace dokuwiki\plugin\totext\Extractor;

/**
 * Contract for all format-specific text extractors.
 */
interface ExtractorInterface
{
    /**
     * Extract plain text from the given file.
     *
     * @param string $path absolute path to the file
     * @return string the extracted plain text
     * @throws \dokuwiki\plugin\totext\Exception\ExtractionException on I/O or parse failure
     */
    public function extract(string $path): string;
}
