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
            'Extract a document to STDOUT. By default prints the body text ' .
            'followed by the metadata as "Key: value" lines. ' .
            'Supported formats: ' . implode(', ', ExtractorFactory::supportedExtensions())
        );
        $options->registerOption(
            'text',
            'Print only the body text (omit metadata).',
            't'
        );
        $options->registerOption(
            'meta',
            'Print only the metadata as "Key: value" lines (omit body text).',
            'm'
        );
        $options->registerArgument('file', 'The file to extract from', true);
    }

    /** @inheritDoc */
    protected function main(Options $options)
    {
        [$file] = $options->getArgs();
        // Any ExtractionException bubbles up; the phpcli CLI base catches it,
        // prints the message to STDERR and exits non-zero.
        $result = ExtractorFactory::extract($file);

        // --text and --meta are mutually exclusive "only this" switches; with
        // neither (the default) both are shown, metadata after the body text.
        $showText = !$options->getOpt('meta');
        $showMeta = !$options->getOpt('text');
        if (!$showText && !$showMeta) {
            $showText = $showMeta = true;
        }

        // A requested half may have failed while the other was salvaged.
        $failures = [];
        if ($showText && $result->textError !== null) {
            $failures['text'] = $result->textError;
        }
        if ($showMeta && $result->metadataError !== null) {
            $failures['metadata'] = $result->metadataError;
        }

        $blocks = [];
        if ($showText && $result->text !== '') {
            $blocks[] = $result->text;
        }
        if ($showMeta && $result->metadata !== []) {
            $lines = [];
            foreach ($result->metadata as $key => $value) {
                $lines[] = "$key: $value";
            }
            $blocks[] = implode("\n", $lines);
        }

        // Nothing usable came through for what was requested: a hard failure.
        // Re-throwing lets the CLI's exception handler report it and exit
        // non-zero (the same path a total failure already takes).
        if ($blocks === [] && $failures !== []) {
            throw reset($failures);
        }

        // Partial success: warn on STDERR about the failed half but still emit
        // the salvaged half on STDOUT. Body text first, then the metadata block,
        // separated by a blank line.
        foreach ($failures as $what => $error) {
            $this->warning("$what extraction failed: " . $error->getMessage());
        }
        echo implode("\n\n", $blocks) . "\n";
        return 0;
    }
}
