# AGENTS.md

This file provides guidance to AI agents when working with code in this repository.

**Always update this file automatically when you learn new things about the code base!**

## Project Overview

This is the totext DokuWiki plugin. It extracts plain text from various file
formats using **PHP only** — no shell-outs and no external binaries.

It exposes two entry points:

1. **CLI component** (`cli.php`, class `cli_plugin_totext`) —
   `php bin/plugin.php totext <file>` prints the extracted text to STDOUT.
2. **Helper component** (`helper.php`, class `helper_plugin_totext`) — gives other
   plugins the same functionality via `plugin_load('helper', 'totext')`.

### Architecture

- `Extractor/ExtractorInterface.php` — the contract every extractor implements.
  It is deliberately just `extract()`: extractors are dumb workers and do not
  know their own extensions. All extension knowledge lives in the factory.
- `Extractor/ExtractorFactory.php` — the sole routing authority (`forFile()`,
  `extract()`, `supportedExtensions()`). Its `EXTRACTORS` constant (extension →
  extractor class) is the single source of truth: `forFile()` looks the file's
  extension up in it and `supportedExtensions()` returns its keys. Add a new
  format with one new extractor class plus one entry in that map — extensions
  are never written down anywhere else.
- `Extractor/AbstractZipXmlExtractor.php` — shared base for all ZIP-of-XML formats
  (OOXML *and* OpenDocument). Provides `readPart()`, `listParts()`, temp-dir
  handling, and two streaming `XMLReader` text walkers: `extractTextFromXml()`
  (text in a wrapper element, used by OOXML) and `extractAllTextFromXml()` (text
  as character data, used by OpenDocument). The unpack temp dir is created in
  DokuWiki's own temp dir (`$conf['tmpdir']`) via core's `io_mktmpdir()` and
  removed with core's `io_rmdir($dir, true)` — never the system temp dir.
  `extract()` removes it promptly in a `finally` block; the class `__destruct()`
  repeats the cleanup as a safety net so the dir is gone even if the process
  dies (e.g. a fatal error) before `finally` runs.
- Concrete extractors: `DocxExtractor`, `XlsxExtractor`, `PptxExtractor`,
  `OdtExtractor`, `OdsExtractor`, `OdpExtractor`, `PdfExtractor`, `TextExtractor`
  (txt/csv/md/log/...), `ImageExtractor` (EXIF/IPTC metadata of jpg/tiff — metadata
  only, not OCR).
- `Exception/ExtractionException.php` and `Exception/UnsupportedFormatException.php`
  (the latter extends the former). The helper and factory **throw**; the CLI does
  not catch, relying on `splitbrain\phpcli\CLI` to print the message and exit non-zero.

### Dependencies

- `smalot/pdfparser` is bundled in the committed `vendor/` directory (pulled via the
  plugin's own `composer.json`). DokuWiki core auto-requires
  `lib/plugins/totext/vendor/autoload.php` for enabled plugins.
- `splitbrain\PHPArchive\Zip` is **not** bundled — core provides it globally.
- Text encoding defers to core's `dokuwiki\Utf8\Clean` / `dokuwiki\Utf8\Conversion`;
  JPEG metadata uses core's `JpegMeta`; TIFF metadata uses the `exif` extension.

Legacy binary Office formats (`.doc`/`.xls`/`.ppt`) are intentionally unsupported.

## Automated Testing

Tests run via DokuWiki's PHPUnit-based testing framework. The calls MUST be made from within the plugin's repository root using a relative path to the `bin/plugin.php` script!

```bash
# Tests must be run from repository root
../../../bin/plugin.php dev test

# run individual test file
../../../bin/plugin.php dev test _test/GeneralTest.php

# create a new test file
../../../bin/plugin.php dev addTest MyClass
```

DokuWiki provides useful helper methods for testing:

* `DokuWikiTest::getInaccessibleProperty()` to access private/protected properties
* `DokuWikiTest::callInaccessibleMethod` to execute private/protected methods
* read `../../../_test/core/DokuWikiTest.php` for more helper methods
* use `../../../_test/TestRequest.php` to simulate HTTP requests for integration tests
* use `../../../_test/phpQuery-onefile.php` if you need to parse HTML in tests

Each test run will provide a fresh DokuWiki instance in a temporary directory via the default setupBeforeClass methods.

### Test fixtures

**All tests run against real files — never hand-built containers.** A
hand-authored fixture only encodes our *belief* about a format; if that belief
is wrong the extractor is written to match and both agree on a fiction. So
every sample is produced by a real application and committed under `_test/data`.
`_test` is `export-ignore`d, so these binaries never ship in release tarballs.

* **Generation** — `_test/data/regenerate.sh` rebuilds the edge-case samples
  from the plain-text sources authored inline in that script: LibreOffice
  headless converts flat-ODF (`.fodt`/`.fods`/`.fodp`) to the office formats,
  and ImageMagick + exiftool produce the images. Requires `soffice`, `exiftool`,
  `magick`. The shared-source happy-path files (`sample.docx`, `sample.xlsx`, …
  used by `SampleFilesTest`) are committed as-is and not rebuilt by the script.
* **What the samples cover** — `sample.*` (one shared source text across
  docx/xlsx/pptx/odt/ods/odp/pdf/txt) for the happy path; `formatting.docx`/
  `formatting.odt` (heading, in-paragraph tab, line break, plus DOCX
  header/footer); `multi-sheet.xlsx` (tab order + name↔file resolution);
  `notes.pptx` (speaker notes); `rich.ods` (merged/covered cells,
  multi-paragraph cell, in-cell tab); `meta.jpg` (IPTC), `meta.tiff`
  (EXIF incl. a UTF-16LE XP tag), `plain.jpg` (no metadata).
* **Error-path tests** — derived from real files, not fabricated: `Samples.php`
  exposes `withoutPart()` (copy a real container and drop one ZIP member, e.g. a
  DOCX missing `word/document.xml`) and `corrupt()` (non-archive bytes), plus
  `path()`/`tempDir()`/`cleanup()`. There is no fixture *builder*.
* **Scenarios real tools cannot produce are not tested** — e.g. an unnamed ODS
  sheet or a repeated *filled-text* cell. LibreOffice never emits them, so
  testing them would mean asserting against invented structures.

### Format-specific gotchas learned

* **XLSX sheet order/names** must be resolved through `xl/workbook.xml` +
  `xl/_rels/workbook.xml.rels` (name → r:id → worksheet file). Worksheet file
  numbering does NOT have to match tab order, so pairing sorted filenames with
  names positionally mismatches them. `XlsxExtractor` follows the relationships
  and only falls back to positional/`SheetN` naming when the workbook or its
  rels are absent.
* **EXIF "XP" tags** (TIFF): PHP's `exif_read_data` already decodes the Windows
  XP tags and exposes them in a `WINXP` section under the short names
  `Title`/`Comment`/`Author`/`Keywords`/`Subject` (UTF-8) — NOT as `XPTitle`
  etc. `ImageExtractor::EXIF_FIELDS` lists the short names first and keeps the
  `XP*` raw names as a cross-version fallback.

**Important:** Test classes that need the plugin must set `protected $pluginsEnabled = ['totext'];` to enable it in the test environment.

**Important:** `setUp()` and `tearDown()` methods must be `public` (not `protected`) to match the `DokuWikiTest` base class.


## Caching

DokuWiki may cache JavaScript, CSS and rendered output. To reset the cache just touch the config file

```bash
touch ../../../conf/local.php
```

## Linting, Formatting and Conventions

Adhere to PSR-12 coding standards. Always add proper docblocks with descriptions, parameter types, and return types to all classes, methods and functions.

```bash
# Lint PHP files using PHP_CodeSniffer (must be run from repo root)
../../../bin/plugin.php dev check

# Auto-Fix formatting issues using PHP_CBF and Rector (must be run from repo root)
../../../bin/plugin.php dev fix
```

## Plugin Architecture

Inspect the base plugin classes in `../../../inc/Extension/` to learn about the plugin system architecture.

```bash
# add new plugin components (must be run from repo root)
../../../bin/plugin.php dev addComponent <type>
# e.g.
../../../bin/plugin.php dev addComponent action
# if multiple of the same type are needed, give a name:
../../../bin/plugin.php dev addComponent action foobar
# -> creates action/foobar.php
```

Additional classes are autoloaded when using the `dokuwiki\plugin\totext` namespace.
