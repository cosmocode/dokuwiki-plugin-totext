<?php

namespace dokuwiki\plugin\totext\Extractor;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use JpegMeta;

/**
 * Extracts textual metadata (IPTC/EXIF) from images.
 *
 * This reads embedded metadata only — it is NOT optical character recognition.
 * JPEG files are read through DokuWiki's core JpegMeta reader; TIFF files (and
 * JPEG as a fallback) are read through the exif extension. If a file carries no
 * textual metadata, an empty string is returned (it was readable, just empty).
 */
final class ImageExtractor implements ExtractorInterface
{
    /**
     * Ordered map of output label => candidate JpegMeta field names.
     *
     * Mirrors the field/lookup chains used by DokuWiki's media manager
     * (see conf/mediameta.php); the first non-empty candidate wins.
     *
     * @var array<string, string[]>
     */
    private const JPEG_FIELDS = [
        'Title' => ['Iptc.Headline'],
        'Caption' => ['Iptc.Caption', 'Exif.UserComment', 'Exif.TIFFImageDescription', 'Exif.TIFFUserComment'],
        'Author' => ['Iptc.Byline', 'Exif.TIFFArtist', 'Exif.Artist', 'Iptc.Credit'],
        'Copyright' => ['Iptc.CopyrightNotice', 'Exif.TIFFCopyright', 'Exif.Copyright'],
        'Keywords' => ['Iptc.Keywords', 'Exif.Category'],
        'Date' => ['Exif.DateTimeOriginal', 'Exif.DateTimeDigitized', 'Exif.DateTime'],
        'Camera' => ['Simple.Camera'],
    ];

    /**
     * Ordered map of output label => candidate EXIF tag names (for TIFF).
     *
     * @var array<string, string[]>
     */
    private const EXIF_FIELDS = [
        'Title' => ['XPTitle'],
        'Caption' => ['ImageDescription', 'UserComment', 'XPComment', 'XPSubject'],
        'Author' => ['Artist', 'XPAuthor'],
        'Copyright' => ['Copyright'],
        'Keywords' => ['XPKeywords'],
        'Date' => ['DateTimeOriginal', 'DateTime'],
        'Camera' => ['Model'],
    ];

    /** @inheritDoc */
    public function supports(string $path): bool
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'tif', 'tiff'], true);
    }

    /** @inheritDoc */
    public function extract(string $path): string
    {
        if (!is_file($path)) {
            throw new ExtractionException("File not found: $path");
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === 'jpg' || $ext === 'jpeg') {
            $fields = $this->extractJpeg($path);
        } else {
            $fields = $this->extractExif($path);
        }

        $lines = [];
        foreach ($fields as $label => $value) {
            if ($value !== '') {
                $lines[] = "$label: $value";
            }
        }
        return implode("\n", $lines);
    }

    /**
     * Read textual metadata from a JPEG via core JpegMeta.
     *
     * @param string $path absolute path to the JPEG
     * @return array<string, string> label => value (empty values kept out by caller)
     */
    private function extractJpeg(string $path): array
    {
        $meta = new JpegMeta($path);
        $out = [];
        foreach (self::JPEG_FIELDS as $label => $candidates) {
            $value = $meta->getField($candidates);
            $out[$label] = is_string($value) ? trim($value) : '';
        }
        return $out;
    }

    /**
     * Read textual metadata from a TIFF (or JPEG fallback) via the exif extension.
     *
     * @param string $path absolute path to the image
     * @return array<string, string> label => value
     * @throws ExtractionException if the exif extension is unavailable
     */
    private function extractExif(string $path): array
    {
        if (!function_exists('exif_read_data')) {
            throw new ExtractionException('TIFF metadata support requires the PHP exif extension');
        }

        $data = @exif_read_data($path, '', true);
        if ($data === false) {
            return [];
        }
        // flatten the section => tag => value structure into a single tag map
        $flat = [];
        foreach ($data as $section) {
            if (is_array($section)) {
                foreach ($section as $tag => $value) {
                    $flat[$tag] = $value;
                }
            }
        }

        $out = [];
        foreach (self::EXIF_FIELDS as $label => $candidates) {
            $value = '';
            foreach ($candidates as $tag) {
                if (isset($flat[$tag]) && $flat[$tag] !== '') {
                    $value = $this->normaliseExifValue($tag, $flat[$tag]);
                    if ($value !== '') {
                        break;
                    }
                }
            }
            $out[$label] = $value;
        }
        return $out;
    }

    /**
     * Normalise a raw EXIF value to a trimmed UTF-8 string.
     *
     * The Windows "XP" tags are stored as UTF-16LE byte strings and need
     * decoding; all other textual tags are returned as-is.
     *
     * @param string $tag the EXIF tag name
     * @param mixed $value the raw value
     * @return string
     */
    private function normaliseExifValue(string $tag, $value): string
    {
        if (is_array($value)) {
            $value = implode(', ', array_filter(array_map('strval', $value), fn($v) => $v !== ''));
        }
        $value = (string) $value;

        if (str_starts_with($tag, 'XP') && function_exists('mb_convert_encoding')) {
            // strip trailing NUL bytes, then decode from UTF-16LE
            $decoded = mb_convert_encoding(rtrim($value, "\0"), 'UTF-8', 'UTF-16LE');
            if (is_string($decoded)) {
                $value = $decoded;
            }
        }

        return trim($value);
    }
}
