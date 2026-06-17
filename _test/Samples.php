<?php

namespace dokuwiki\plugin\totext\test;

/**
 * Helpers for tests that run against the committed real-world sample files in
 * _test/data.
 *
 * This is deliberately NOT a fixture builder: it never fabricates office
 * container structures. The samples are produced by real applications
 * (LibreOffice, ImageMagick + exiftool — see data/regenerate.sh), so the
 * extractors are exercised against the byte layout real software emits.
 *
 * For error-path tests it derives broken inputs from those real files — a
 * genuine container with one part removed, or plain non-archive bytes — which
 * likewise requires no knowledge of the internal format.
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
     * Create a fresh temporary directory for derived or throwaway files.
     *
     * @return string the directory path
     */
    public static function tempDir(): string
    {
        return io_mktmpdir();
    }

    /**
     * Recursively remove a temporary directory.
     *
     * @param string $dir directory to remove
     * @return void
     */
    public static function cleanup(string $dir): void
    {
        if ($dir !== '') {
            io_rmdir($dir, true);
        }
    }

    /**
     * Copy a committed sample into $destDir with one ZIP member removed.
     *
     * Builds a "real container missing a required part" input for error-path
     * tests, starting from a genuine file rather than a hand-built archive.
     *
     * @param string $sample committed sample file name (a ZIP container)
     * @param string $part internal path of the member to drop
     * @param string $destDir directory to write the copy into
     * @return string path to the derived copy
     */
    public static function withoutPart(string $sample, string $part, string $destDir): string
    {
        $dest = $destDir . '/' . $sample;
        copy(self::path($sample), $dest);
        $zip = new \ZipArchive();
        $zip->open($dest);
        $zip->deleteName($part);
        $zip->close();
        return $dest;
    }

    /**
     * Write non-archive garbage bytes to simulate a corrupt container.
     *
     * @param string $destPath path to write
     * @return string the written path
     */
    public static function corrupt(string $destPath): string
    {
        file_put_contents($destPath, 'this is not a valid office container');
        return $destPath;
    }
}
