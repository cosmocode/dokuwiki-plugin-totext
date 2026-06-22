<?php

namespace dokuwiki\plugin\totext\Extractor;

use dokuwiki\plugin\totext\Exception\UnsupportedFormatException;

/**
 * Picks the right extractor for a file based on its extension.
 */
class ExtractorFactory
{
    /**
     * Maps each supported file extension to its extractor class.
     *
     * This is the single source of truth for the formats the plugin handles and
     * the only routing authority: forFile() looks a file's extension up here and
     * supportedExtensions() lists the keys. Add a format with one new extractor
     * class plus one entry below.
     *
     * @var array<string, class-string<ExtractorInterface>>
     */
    protected const EXTRACTORS = [
        'docx' => DocxExtractor::class,
        'xlsx' => XlsxExtractor::class,
        'pptx' => PptxExtractor::class,
        'odt' => OdtExtractor::class,
        'ods' => OdsExtractor::class,
        'odp' => OdpExtractor::class,
        'pdf' => PdfExtractor::class,
        'txt' => TextExtractor::class,
        'csv' => TextExtractor::class,
        'md' => TextExtractor::class,
        'markdown' => TextExtractor::class,
        'log' => TextExtractor::class,
        'text' => TextExtractor::class,
        'jpg' => ImageExtractor::class,
        'jpeg' => ImageExtractor::class,
        'tif' => ImageExtractor::class,
        'tiff' => ImageExtractor::class,
    ];

    /**
     * Return an Extractor for the given file, based on its extension.
     *
     * @param string $path file name or path
     * @return ExtractorInterface
     * @throws UnsupportedFormatException if the extension is not recognised
     */
    public static function forFile(string $path): ExtractorInterface
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!isset(self::EXTRACTORS[$ext])) {
            throw new UnsupportedFormatException(
                $ext === ''
                    ? "Cannot determine format: no file extension on $path"
                    : "Unsupported file extension: .$ext",
            );
        }
        $class = self::EXTRACTORS[$ext];
        return new $class();
    }

    /**
     * Convenience: pick the right extractor and run it.
     *
     * @param string $path absolute path to the file
     * @return ExtractionResult the extracted body text and canonical metadata
     * @throws \dokuwiki\plugin\totext\Exception\ExtractionException on failure or unsupported format
     */
    public static function extract(string $path): ExtractionResult
    {
        return self::forFile($path)->extract($path);
    }

    /**
     * List all file extensions this factory can route to an extractor.
     *
     * @return string[] supported extensions (without leading dot)
     */
    public static function supportedExtensions(): array
    {
        return array_keys(self::EXTRACTORS);
    }
}
