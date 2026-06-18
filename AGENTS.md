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

**All tests run against real files — never hand-built containers, never
synthetic content.** A hand-authored fixture only encodes our *belief* about a
format; if that belief is wrong the extractor is written to match and both agree
on a fiction. So every sample is a genuine in-the-wild document, taken verbatim
from the **Apache Tika test corpus** (Apache-2.0) and committed under
`_test/data`. `_test` is `export-ignore`d, so these binaries never ship in
release tarballs — only the git repo holds them.

* **Provenance** — `_test/data/README.md` records, for every committed file, its
  original Tika name + upstream path + license. Keep it accurate when files
  change: committing third-party binaries here is fine *only* with that record.
* **Refresh** — `_test/data/download.sh` re-downloads each file from its exact
  upstream Tika path (needs only `curl`). The committed copies are authoritative;
  the script just refreshes them and must reproduce them byte-for-byte. Every
  committed file keeps a `tika-` filename prefix to mark its origin.
* **What the samples cover** — `tika-sample.docx` (titles, headings, nested
  tables, hyperlinks, custom style, header/footer); `tika-various.docx` (lists,
  footnotes, Japanese + four-byte Gothic = multibyte UTF-8); `tika-sample.xlsx`
  (three named sheets, tab-separated cells); `tika-sample.pptx` (three ordered
  slides); `tika-various.pptx` (speaker notes); `tika-sample.odt` (one
  paragraph); `tika-sample.ods` (numeric grid); `tika-sample.odp` (two slides);
  `tika-sample.pdf` (the Tika homepage, full prose); `tika-meta.jpg` (Photoshop
  IPTC caption/by-line/keywords + EXIF camera — IPTC is non-UTF-8, so only
  ASCII-safe substrings are asserted); `tika-meta.tiff` (EXIF ImageDescription);
  `tika-plain.jpg` (no metadata → empty string); `tika-sample.txt` (multilingual
  UTF-8 pangram).
* **Error-path tests** — derived from real files, not fabricated: `Samples.php`
  exposes `withoutPart()` (unpack a real container minus one ZIP member and repack
  it via php-archive, e.g. a DOCX missing `word/document.xml`) and `corrupt()`
  (non-archive bytes); both manage their own temp files, and `path()` resolves a
  committed sample. There is no fixture *builder*.
* **Edges no clean real file covers are not tested** — deliberately dropped when
  the corpus had no document that targets them without inventing structure: XLSX
  custom sheet-name↔file *re*ordering (only normal multi-sheet order is tested),
  ODS merged/covered cells + multi-paragraph cells, and the Windows XP UTF-16LE
  EXIF tag. Asserting these would mean asserting against invented inputs.

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
  `XP*` raw names (decoded from UTF-16LE by `normaliseExifValue()`) as a
  cross-version fallback. This path still exists but is **no longer covered by a
  fixture** — no real image in the corpus carries Windows XP tags.

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
