# AGENTS.md

This file provides guidance to AI agents when working with code in this repository.

**Always update this file automatically when you learn new things about the code base!**

## Project Overview

This is the totext DokuWiki plugin. It extracts plain text from various file
formats using **PHP only** — no shell-outs and no external binaries.

It exposes two entry points:

1. **CLI component** (`cli.php`, class `cli_plugin_totext`) —
   `php bin/plugin.php totext <file>` prints the body text followed by the
   metadata (`Key: value` lines, separated by a blank line) to STDOUT. `--text`
   (`-t`) prints only the body text; `--meta` (`-m`) prints only the metadata;
   the two are mutually exclusive "only this" switches. If a requested half fails
   but the other is salvaged, it warns on STDERR and still prints the salvaged
   half (exit 0); if *nothing* usable came through for what was requested it
   re-throws, so the CLI exception handler reports it and exits non-zero.
2. **Helper component** (`helper.php`, class `helper_plugin_totext`) — gives other
   plugins the same functionality via `plugin_load('helper', 'totext')`:
   `extract($path): ExtractionResult` (both outputs; never throws for a *partial*
   failure — inspect `->textError`/`->metadataError`), `extractMetadata($path):
   array` (throws if metadata failed), and `extractText($path): string` (throws
   if text failed — preserves the throw-on-failure contract the docsearch plugin
   relies on to fall back to its own converters).

### Architecture

- `Extractor/ExtractorInterface.php` — the contract every extractor implements.
  It is deliberately just `extract(): ExtractionResult`: extractors are dumb
  workers and do not know their own extensions. All extension knowledge lives in
  the factory.
- `Extractor/ExtractionResult.php` — the `final` readonly value object every
  extractor returns: `string $text` (body text), `array<string,string>
  $metadata` (canonical key → value map), and `?\Throwable $textError` /
  `?\Throwable $metadataError` recording a per-half failure (plus `isComplete()`).
  Produced from **one** parse of the file. See *Metadata* below for the
  vocabulary and *Failure model* below for the error semantics.
- `Extractor/ExtractorFactory.php` — the sole routing authority (`forFile()`,
  `extract()` → `ExtractionResult`, `supportedExtensions()`). Its `EXTRACTORS`
  constant (extension → extractor class) is the single source of truth:
  `forFile()` looks the file's extension up in it and `supportedExtensions()`
  returns its keys. Add a new format with one new extractor class plus one entry
  in that map — extensions are never written down anywhere else.
- `Extractor/AbstractZipXmlExtractor.php` — shared base for all ZIP-of-XML formats
  (OOXML *and* OpenDocument). `extract()` does one unzip — **opening the archive
  is the total-failure gate**: if it can't be unpacked, nothing is recoverable
  and it throws. Once open, the abstract `extractText()` and `extractMetadata()`
  run as independent halves; whichever throws has its error recorded on the
  result (normalised to an `ExtractionException` via `ExtractionException::wrap()`,
  the shared wrap-a-caught-error helper) while the other half is still returned
  (see *Failure model*). Provides
  `readPart()`, `listParts()`, temp-dir handling, two streaming `XMLReader` text
  walkers — `extractTextFromXml()` (text in a wrapper element, used by OOXML) and
  `extractAllTextFromXml()` (text as character data, used by OpenDocument) — and
  the generic `mapMetadataFromXml($xml, $map, $multiValueKeys)` primitive (walks
  a metadata part, matching element **local names** to canonical keys, dropping
  empty values, accumulating multi-value keys). The unpack temp dir is created in
  DokuWiki's own temp dir (`$conf['tmpdir']`) via core's `io_mktmpdir()` and
  removed with core's `io_rmdir($dir, true)` — never the system temp dir.
  `extract()` removes it promptly in a `finally` block; the class `__destruct()`
  repeats the cleanup as a safety net so the dir is gone even if the process
  dies (e.g. a fatal error) before `finally` runs.
- `Extractor/AbstractOoxmlExtractor.php` / `Extractor/AbstractOdfExtractor.php` —
  intermediary classes between the ZIP base and the concrete extractors. They
  exist solely to declare each family's metadata source **once**: OOXML reads
  `docProps/core.xml` (`CORE_META_MAP`) + `docProps/app.xml` (`APP_META_MAP`);
  ODF reads `meta.xml` (`META_MAP`, with `meta:keyword` accumulating into
  `Keywords`). There is no family autodetection — each concrete ZIP extractor
  simply `extends` the matching intermediary and carries no metadata code.
- Concrete extractors: `DocxExtractor`/`XlsxExtractor`/`PptxExtractor` (extend
  `AbstractOoxmlExtractor`), `OdtExtractor`/`OdsExtractor`/`OdpExtractor` (extend
  `AbstractOdfExtractor`), `PdfExtractor`, `TextExtractor` (txt/csv/md/log/...),
  `ImageExtractor` (EXIF/IPTC metadata of jpg/tiff — metadata only, not OCR).
- `Exception/ExtractionException.php` and `Exception/UnsupportedFormatException.php`
  (the latter extends the former). The helper and factory **throw**; the CLI does
  not catch, relying on `splitbrain\phpcli\CLI` to print the message and exit non-zero.

### Metadata

`extract()` returns a metadata map keyed by a **single canonical vocabulary**,
identical across all formats so consumers never special-case the source:

`Title, Author, Subject, Keywords, Description, Created, Modified, Language,
Producer` (all formats) plus `Copyright` (**image-only**). Values are non-empty
UTF-8 strings; empty values are dropped (never stored as blank keys).

- **`Producer`** is "what produced this file" — the authoring application for
  office/PDF (PDF: `Producer`, else `Creator`) **and** the camera/software for an
  image (`Simple.Camera` / EXIF `Model`). There is intentionally no separate
  `Camera` key.
- **`Copyright` is image-only by spec, not by accident.** OOXML
  `CT_CoreProperties` (ECMA-376 / ISO 29500) has no `dc:rights`; ODF
  `<office:meta>` has no `dc:rights` either; the PDF Info dictionary has no
  copyright key (it lives only in an XMP stream prinsfrank does not surface).
  Only IPTC/EXIF have dedicated copyright fields, which `ImageExtractor` reads.
- Each family's source map is declared **once** in its intermediary class
  (`AbstractOoxmlExtractor`, `AbstractOdfExtractor`); `PdfExtractor` and
  `ImageExtractor` map their own native fields. The generic
  `mapMetadataFromXml()` matches by element **local name** (namespace-agnostic) —
  safe because OOXML core/app and ODF meta use distinct local names.
- **Image behavior change:** `extractText()` on an image now returns `''` (its
  descriptive fields moved into `metadata`), not the old `"Caption: …\nAuthor:
  …"` text block. `ImageExtractor`'s field-map keys were renamed to the canonical
  vocabulary (`Caption`→`Description`, `Date`→`Created`, `Camera`→`Producer`).

### Failure model

Text and metadata are extracted **independently**, so one half can fail while
the other is salvaged. Each extractor has a single **total-failure gate** — the
step that, if it fails, leaves *nothing* recoverable (the container won't unzip,
the PDF won't parse, the file can't be read). The gate throws an
`ExtractionException`. Everything after the gate is a per-half best-effort:

- A half that throws has its error recorded in `ExtractionResult::$textError` /
  `$metadataError`; its output is left empty (`''` / `[]`) and the *other* half
  is still returned. `extract()` itself does **not** throw for a partial failure.
- An output that is empty **by design** is not a failure and leaves its error
  `null`: images have no body text (`text === ''`, `textError === null`), plain
  text has no metadata (`metadata === []`, `metadataError === null`). Likewise a
  document that simply carries no metadata yields `[]` with no error — only an
  actual read/parse exception sets `metadataError`.
- Single-output formats fold into this cleanly: their one product *is* the gate.
  `TextExtractor` (text-only) and `ImageExtractor` (metadata-only) throw on
  failure and never set the error fields.
- The helper's `extractText()` / `extractMetadata()` **re-throw** the relevant
  half's recorded error, so a single-output caller keeps its throw-on-failure
  contract even when the other half was salvaged. The CLI warns on STDERR about
  a failed half but still prints the salvaged half (exit 0); only when *nothing*
  usable came through for what was requested does it re-throw (non-zero exit).

### Dependencies

- `prinsfrank/pdfparser` (MIT, zero PHP dependencies — pulls only
  `prinsfrank/glyph-lists`) is bundled in the committed `vendor/` directory (pulled
  via the plugin's own `composer.json`). We track the **cosmocode fork's `dev`
  branch** (`dev-dev`, VCS repo `github.com/cosmocode/prinsfrank-pdfparser`) rather
  than an upstream tag: it carries fixes not yet released upstream (native UTF-16BE
  Info-string decoding and Form-XObject text extraction, both noted below).
  DokuWiki core auto-requires
  `lib/plugins/totext/vendor/autoload.php` for enabled plugins. `PdfExtractor`
  parses once via `(new PrinsFrank\PdfParser\PdfParser())->parseFile($path)`, takes
  the body from `->getText()` and reads the Info dictionary
  (`getInformationDictionary()`) best-effort for metadata — the default in-memory
  mode, which benchmarked both faster and far lighter than the previous
  smalot/cosmocode fork (no `setRetainImageContent` tuning needed). It requires
  `ext-gd`/`ext-iconv`/`ext-zlib`, enforced transitively by the package.
- **UTF-16BE Info strings** decode natively on the cosmocode `dev` fork, so
  `extractMetadata()` just `trim()`s the values — no shim. (`tika-sample.pdf`'s
  Author `Bertrand Delacrétaz` exercises this: the é comes through correctly.)
  Stock upstream ≤ v3.1.0 needed a `normalizePdfString()` shim here (mojibake `þÿ`
  → ISO-8859-1 → `iconv` UTF-16BE→UTF-8); it was removed when we moved to the fork.
- **Form-XObject text** (page content painted with the `Do` operator — common in
  Quartz/macOS, Firefox and Chrome PDFs, including `_test/data/tika-sample.pdf`) is
  extracted by the cosmocode `dev` fork; `PdfExtractorTest::testExtractsText` and the
  factory roundtrip's `pdf` case rely on it. Stock upstream ≤ v3.1.0 does **not**
  extract this text (metadata was unaffected either way, since the Info dictionary is
  read independently of `getText()`).
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
  committed sample. There is no fixture *builder*. Note the failure model (above):
  `corrupt()` trips the total-failure gate so `extract()` **throws**, whereas
  `withoutPart()` removing only the body part (`word/document.xml`, `content.xml`)
  leaves the container openable, so `extract()` returns a **partial result** —
  `textError` set, the independent metadata (`docProps/*`, `meta.xml`) salvaged.
  `HelperTest` separately locks the `extractText()` re-throw contract.
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
