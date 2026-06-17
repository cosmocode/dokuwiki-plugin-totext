<?php

namespace dokuwiki\plugin\totext\Extractor;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use dokuwiki\Utf8\Clean;
use dokuwiki\Utf8\Conversion;

/**
 * Extracts text from the plain-text family of files (txt, csv, md, log, ...).
 *
 * Line endings are normalised and the content is returned as UTF-8. Encoding
 * handling defers to DokuWiki's core UTF-8 helpers so behaviour matches the
 * rest of the wiki.
 */
final class TextExtractor implements ExtractorInterface
{
    /** @inheritDoc */
    public function supports(string $path): bool
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, ['txt', 'csv', 'md', 'markdown', 'log', 'text'], true);
    }

    /** @inheritDoc */
    public function extract(string $path): string
    {
        if (!is_file($path)) {
            throw new ExtractionException("File not found: $path");
        }
        $data = file_get_contents($path);
        if ($data === false) {
            throw new ExtractionException("Could not read file: $path");
        }

        // Ensure UTF-8: convert from Latin-1 if the bytes are not valid UTF-8,
        // then sanitise any remaining stray bytes.
        if (!Clean::isUtf8($data)) {
            $data = Conversion::fromLatin1($data);
        }
        $data = Clean::replaceBadBytes($data);

        // Normalise line endings.
        $data = str_replace(["\r\n", "\r"], "\n", $data);

        return trim($data);
    }
}
