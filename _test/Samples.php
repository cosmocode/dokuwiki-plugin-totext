<?php

namespace dokuwiki\plugin\totext\test;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use splitbrain\PHPArchive\Zip;

/**
 * Helpers for tests that run against the committed real-world sample files in
 * _test/data.
 *
 * This is deliberately NOT a fixture builder: it never fabricates office
 * container structures. The samples are real documents taken verbatim from the
 * Apache Tika test corpus (see data/README.md for provenance). For error-path
 * tests it derives broken inputs from those real files — a genuine container
 * with one part removed, or plain non-archive bytes.
 */
class Samples
{
    /** absolute path to the committed sample data directory */
    public const DIR = __DIR__ . '/data';

    /**
     * Absolute path to a committed sample file.
     *
     * @param string $name file name within _test/data
     * @return string
     */
    public static function path(string $name): string
    {
        return self::DIR . '/' . $name;
    }

    /**
     * Build a copy of a committed sample with one ZIP member removed.
     *
     * Produces a "real container missing a required part" input for error-path
     * tests, starting from a genuine file rather than a hand-built archive. Uses
     * DokuWiki's bundled php-archive (no dependency on PHP's ext-zip): the
     * original is unpacked with the part excluded, then repacked.
     *
     * @param string $sample committed sample file name (a ZIP container)
     * @param string $part internal path of the member to drop
     * @return string path to the derived copy (in a fresh temp dir)
     */
    public static function withoutPart(string $sample, string $part): string
    {
        $tmp = io_mktmpdir();
        $work = $tmp . '/unpacked';

        $unzip = new Zip();
        $unzip->open(self::path($sample));
        $unzip->extract($work, '', '/^' . preg_quote($part, '/') . '$/');

        $dest = $tmp . '/' . $sample;
        $rezip = new Zip();
        $rezip->create($dest);
        $base = $work . '/';
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($work, FilesystemIterator::SKIP_DOTS),
        );
        foreach ($it as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $rel = str_replace('\\', '/', substr($file->getPathname(), strlen($base)));
            $rezip->addFile($file->getPathname(), $rel);
        }
        $rezip->close();

        return $dest;
    }

    /**
     * Write non-archive garbage bytes to simulate a corrupt container.
     *
     * @param string $ext extension to give the file, so callers that route by
     *                     extension (e.g. ExtractorFactory) reach the right extractor
     * @return string path to the written file (in a fresh temp dir)
     */
    public static function corrupt(string $ext = 'docx'): string
    {
        $path = io_mktmpdir() . '/corrupt.' . $ext;
        file_put_contents($path, 'this is not a valid office container');
        return $path;
    }
}
