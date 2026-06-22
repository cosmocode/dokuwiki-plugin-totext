<?php

use dokuwiki\plugin\totext\Extractor\ExtractionResult;
use dokuwiki\plugin\totext\Extractor\ExtractorFactory;

/**
 * DokuWiki Plugin totext (Helper Component)
 *
 * Gives other plugins a simple API to extract plain text and metadata from
 * documents.
 *
 * @license GPL-2.0-only
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */
class helper_plugin_totext extends \dokuwiki\Extension\Plugin
{
    /**
     * Extract body text and metadata from the given file.
     *
     * @param string $path absolute path to the file
     * @return ExtractionResult the extracted body text and canonical metadata
     * @throws \dokuwiki\plugin\totext\Exception\ExtractionException on failure
     * @throws \dokuwiki\plugin\totext\Exception\UnsupportedFormatException on unsupported format
     */
    public function extract($path)
    {
        return ExtractorFactory::extract($path);
    }

    /**
     * Extract plain text from the given file.
     *
     * Unlike extract(), this throws when text extraction itself failed (even if
     * metadata was salvaged), so callers that want only the text — e.g. the
     * docsearch plugin, which falls back to its own converters on failure — keep
     * their throw-on-failure contract.
     *
     * @param string $path absolute path to the file
     * @return string the extracted plain text
     * @throws \dokuwiki\plugin\totext\Exception\ExtractionException on failure
     * @throws \dokuwiki\plugin\totext\Exception\UnsupportedFormatException on unsupported format
     */
    public function extractText($path)
    {
        $result = $this->extract($path);
        if ($result->textError !== null) {
            throw $result->textError;
        }
        return $result->text;
    }

    /**
     * Extract the canonical metadata map from the given file.
     *
     * Throws when metadata extraction itself failed (even if the body text was
     * extracted), mirroring extractText().
     *
     * @param string $path absolute path to the file
     * @return array<string, string> canonical key => value map
     * @throws \dokuwiki\plugin\totext\Exception\ExtractionException on failure
     * @throws \dokuwiki\plugin\totext\Exception\UnsupportedFormatException on unsupported format
     */
    public function extractMetadata($path)
    {
        $result = $this->extract($path);
        if ($result->metadataError !== null) {
            throw $result->metadataError;
        }
        return $result->metadata;
    }

    /**
     * List the file extensions this plugin can handle.
     *
     * @return string[] supported extensions (without leading dot)
     */
    public function supportedExtensions()
    {
        return ExtractorFactory::supportedExtensions();
    }

    /**
     * Whether the given file is supported, based on its extension.
     *
     * @param string $path file name or path
     * @return bool
     */
    public function isSupported($path)
    {
        return in_array(
            strtolower(pathinfo($path, PATHINFO_EXTENSION)),
            $this->supportedExtensions(),
            true
        );
    }
}
