<?php

namespace dokuwiki\plugin\totext\Extractor;

use dokuwiki\plugin\totext\Exception\UnsupportedFormatException;

/**
 * Picks the right extractor for a file based on its extension.
 */
final class ExtractorFactory
{
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
        return match ($ext) {
            'docx' => new DocxExtractor(),
            'xlsx' => new XlsxExtractor(),
            'pptx' => new PptxExtractor(),
            'odt' => new OdtExtractor(),
            'ods' => new OdsExtractor(),
            'odp' => new OdpExtractor(),
            'pdf' => new PdfExtractor(),
            'txt', 'csv', 'md', 'markdown', 'log', 'text' => new TextExtractor(),
            'jpg', 'jpeg', 'tif', 'tiff' => new ImageExtractor(),
            default => throw new UnsupportedFormatException(
                $ext === ''
                    ? "Cannot determine format: no file extension on $path"
                    : "Unsupported file extension: .$ext",
            ),
        };
    }

    /**
     * Convenience: pick the right extractor and run it.
     *
     * @param string $path absolute path to the file
     * @return string the extracted plain text
     * @throws \dokuwiki\plugin\totext\Exception\ExtractionException on failure or unsupported format
     */
    public static function extract(string $path): string
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
        return [
            'docx', 'xlsx', 'pptx',
            'odt', 'ods', 'odp',
            'pdf',
            'txt', 'csv', 'md', 'markdown', 'log', 'text',
            'jpg', 'jpeg', 'tif', 'tiff',
        ];
    }
}
