<?php

use dokuwiki\plugin\totext\Extractor\ExtractorFactory;
use splitbrain\phpcli\Options;

/**
 * DokuWiki Plugin totext (CLI Component)
 *
 * Prints the plain text extracted from a document to STDOUT.
 *
 * @license GPL-2.0-only
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */
class cli_plugin_totext extends \dokuwiki\Extension\CLIPlugin
{
    /** @inheritDoc */
    protected function setup(Options $options)
    {
        $options->setHelp(
            'Extract plain text from a document and print it to STDOUT. ' .
            'Supported formats: ' . implode(', ', ExtractorFactory::supportedExtensions())
        );
        $options->registerArgument('file', 'The file to extract text from', true);
    }

    /** @inheritDoc */
    protected function main(Options $options)
    {
        [$file] = $options->getArgs();
        // Any ExtractionException bubbles up; the phpcli CLI base catches it,
        // prints the message to STDERR and exits non-zero.
        echo ExtractorFactory::extract($file);
        echo "\n";
        return 0;
    }
}
