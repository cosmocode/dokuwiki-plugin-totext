<?php

namespace dokuwiki\plugin\totext\Extractor;

use dokuwiki\plugin\totext\Exception\ExtractionException;

/**
 * Extracts text from OpenDocument Text (.odt) documents.
 */
final class OdtExtractor extends AbstractZipXmlExtractor
{
    /** @inheritDoc */
    protected function extractText(): string
    {
        $content = $this->readPart('content.xml');
        if ($content === null) {
            throw new ExtractionException('Not a valid ODT file: missing content.xml');
        }

        return trim($this->extractAllTextFromXml(
            $content,
            blockElements: ['p', 'h', 'line-break'],
            tabElements: ['tab'],
        ));
    }
}
