<?php

use dokuwiki\plugin\totext\Extractor\ExtractorFactory;

/**
 * DokuWiki Plugin totext (Helper Component)
 *
 * Gives other plugins a simple API to extract plain text from documents.
 *
 * @license GPL-2.0-only
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */
class helper_plugin_totext extends \dokuwiki\Extension\Plugin
{
    /**
     * Extract plain text from the given file.
     *
     * @param string $path absolute path to the file
     * @return string the extracted plain text
     * @throws \dokuwiki\plugin\totext\Exception\ExtractionException on failure
     * @throws \dokuwiki\plugin\totext\Exception\UnsupportedFormatException on unsupported format
     */
    public function extractText($path)
    {
        return ExtractorFactory::extract($path);
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
