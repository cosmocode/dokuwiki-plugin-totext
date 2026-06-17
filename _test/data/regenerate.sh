#!/usr/bin/env bash
#
# Regenerates the edge-case sample files used by the extractor tests.
#
# Every binary here is produced by a *real* application (LibreOffice for the
# office formats, ImageMagick + exiftool for the images) from the plain-text
# sources authored inline below. We deliberately do NOT hand-build office ZIP
# structures: the whole point is that the extractors are tested against the
# byte layout a real application emits, so the fixtures cannot drift into a
# self-consistent fiction.
#
# The shared-source happy-path samples (sample.docx, sample.xlsx, ...) used by
# SampleFilesTest are NOT regenerated here; they are committed as-is.
#
# Usage:  ./regenerate.sh
# Requires: soffice (LibreOffice), exiftool, ImageMagick (convert).

set -euo pipefail

DATA="$(cd "$(dirname "$0")" && pwd)"
SRC="$(mktemp -d)"
PROFILE="$(mktemp -d)"
trap 'rm -rf "$SRC" "$PROFILE"' EXIT

# Convert a flat-ODF source to a zipped office format via headless LibreOffice.
# $1 = source file, $2 = target filter/extension, $3 = final filename in DATA
convert_office() {
    local src="$1" fmt="$2" out="$3"
    soffice -env:UserInstallation="file://$PROFILE" --headless \
        --convert-to "$fmt" --outdir "$SRC" "$src" >/dev/null 2>&1
    mv "$SRC/$(basename "${src%.*}").${fmt%%:*}" "$DATA/$out"
    echo "  generated $out"
}

echo "Authoring sources..."

# --- One word-processing source -> both formatting.docx and formatting.odt.
# Body exercises a heading, an in-paragraph tab and a line break (read from
# content.xml by both extractors); the header/footer land in styles and so only
# surface in the DOCX, whose extractor also scans word/header*.xml/footer*.xml. ---
cat > "$SRC/formatting.fodt" <<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<office:document xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
  xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0"
  xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"
  xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0"
  office:version="1.2" office:mimetype="application/vnd.oasis.opendocument.text">
 <office:automatic-styles>
  <style:page-layout style:name="pm1">
   <style:page-layout-properties fo:page-width="21cm" fo:page-height="29.7cm" fo:margin="2cm"/>
   <style:header-style/>
   <style:footer-style/>
  </style:page-layout>
 </office:automatic-styles>
 <office:master-styles>
  <style:master-page style:name="Standard" style:page-layout-name="pm1">
   <style:header><text:p>Document header text</text:p></style:header>
   <style:footer><text:p>Page footer text</text:p></style:footer>
  </style:master-page>
 </office:master-styles>
 <office:body>
  <office:text>
   <text:h text:outline-level="1">Heading One</text:h>
   <text:p>Body paragraph text</text:p>
   <text:p>Tab<text:tab/>separated</text:p>
   <text:p>Line one<text:line-break/>line two</text:p>
  </office:text>
 </office:body>
</office:document>
XML

# --- XLSX with two sheets, "Beta" before "Alpha" (exercises name<->file rels) ---
cat > "$SRC/multi-sheet.fods" <<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<office:document xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
  xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0"
  xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"
  office:version="1.2" office:mimetype="application/vnd.oasis.opendocument.spreadsheet">
 <office:body><office:spreadsheet>
  <table:table table:name="Beta">
   <table:table-row><table:table-cell office:value-type="string"><text:p>BetaCell</text:p></table:table-cell></table:table-row>
  </table:table>
  <table:table table:name="Alpha">
   <table:table-row><table:table-cell office:value-type="string"><text:p>AlphaCell</text:p></table:table-cell></table:table-row>
  </table:table>
 </office:spreadsheet></office:body>
</office:document>
XML

# --- PPTX with one slide carrying speaker notes ---
cat > "$SRC/notes.fodp" <<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<office:document xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
  xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0"
  xmlns:presentation="urn:oasis:names:tc:opendocument:xmlns:presentation:1.0"
  xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"
  office:version="1.2" office:mimetype="application/vnd.oasis.opendocument.presentation">
 <office:body><office:presentation>
  <draw:page draw:name="page1">
   <draw:frame><draw:text-box>
    <text:p>Slide with notes</text:p>
    <text:p>Visible slide body</text:p>
   </draw:text-box></draw:frame>
   <presentation:notes>
    <draw:frame><draw:text-box>
     <text:p>These are the speaker notes.</text:p>
    </draw:text-box></draw:frame>
   </presentation:notes>
  </draw:page>
 </office:presentation></office:body>
</office:document>
XML

# --- ODS with merged cells, a multi-paragraph cell and an in-cell tab ---
cat > "$SRC/rich.fods" <<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<office:document xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
  xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0"
  xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"
  office:version="1.2" office:mimetype="application/vnd.oasis.opendocument.spreadsheet">
 <office:body><office:spreadsheet>
  <table:table table:name="Edge">
   <table:table-row>
    <table:table-cell table:number-columns-spanned="3" office:value-type="string"><text:p>Merged</text:p></table:table-cell>
    <table:covered-table-cell/>
    <table:covered-table-cell/>
    <table:table-cell office:value-type="string"><text:p>After</text:p></table:table-cell>
   </table:table-row>
   <table:table-row>
    <table:table-cell office:value-type="string"><text:p>Line1</text:p><text:p>Line2</text:p></table:table-cell>
    <table:table-cell office:value-type="string"><text:p>X<text:tab/>Y</text:p></table:table-cell>
   </table:table-row>
  </table:table>
 </office:spreadsheet></office:body>
</office:document>
XML

echo "Converting office formats with LibreOffice..."
convert_office "$SRC/formatting.fodt"    docx formatting.docx
convert_office "$SRC/formatting.fodt"    odt  formatting.odt
convert_office "$SRC/multi-sheet.fods"   xlsx multi-sheet.xlsx
convert_office "$SRC/notes.fodp"         pptx notes.pptx
convert_office "$SRC/rich.fods"          ods  rich.ods

echo "Generating images with ImageMagick + exiftool..."
# JPEG carrying IPTC metadata
magick -size 32x32 xc:'#4080c0' "$DATA/meta.jpg"
exiftool -q -overwrite_original \
    -IPTC:Headline='Sample Image Title' \
    -IPTC:Caption-Abstract='A descriptive caption' \
    -IPTC:By-line='Jane Photographer' \
    -IPTC:CopyrightNotice='Copyright ACME' \
    -IPTC:Keywords='alpha' -IPTC:Keywords='beta' \
    "$DATA/meta.jpg"
echo "  generated meta.jpg"

# Plain JPEG with no metadata at all (extracts to an empty string)
magick -size 16x16 xc:gray -strip "$DATA/plain.jpg"
echo "  generated plain.jpg"

# TIFF carrying EXIF metadata, including Windows XP tags (stored UTF-16LE)
magick -size 32x32 xc:white "$DATA/meta.tiff"
exiftool -q -overwrite_original \
    -EXIF:ImageDescription='A TIFF caption' \
    -EXIF:Artist='Tina Tiff' \
    -EXIF:Copyright='Copyright TIFFCorp' \
    -XPTitle='XP Title' \
    -XPKeywords='alpha;beta' \
    "$DATA/meta.tiff"
echo "  generated meta.tiff"

printf 'Plain text sample\nsecond line\n' > "$DATA/sample.txt"
echo "  generated sample.txt"

echo "Done."
