#!/usr/bin/env bash
#
# Re-downloads the test sample files from the Apache Tika test corpus.
#
# Usage:  ./download.sh
# Requires: curl.

set -euo pipefail

DATA="$(cd "$(dirname "$0")" && pwd)"
BASE="https://raw.githubusercontent.com/apache/tika/main/tika-parsers/tika-parsers-standard"
MS="tika-parsers-standard-modules/tika-parser-microsoft-module/src/test/resources/test-documents"
MISC="tika-parsers-standard-modules/tika-parser-miscoffice-module/src/test/resources/test-documents"
PDF="tika-parsers-standard-modules/tika-parser-pdf-module/src/test/resources/test-documents"
IMG="tika-parsers-standard-modules/tika-parser-image-module/src/test/resources/test-documents"
XMP="tika-parsers-standard-modules/tika-parser-xmp-commons/src/test/resources/test-documents"
TXT="tika-parsers-standard-integration-tests/src/test/resources/test-documents"

# fetch <upstream-relative-path> <local-filename>
fetch() {
    curl -sSL --fail -o "$DATA/$2" "$BASE/$1"
    echo "  fetched $2"
}

echo "Downloading samples from Apache Tika..."
fetch "$MS/testWORD.docx"                             tika-sample.docx
fetch "$MS/testWORD_various.docx"                     tika-various.docx
fetch "$MS/testEXCEL.xlsx"                            tika-sample.xlsx
fetch "$MS/testPPT.pptx"                              tika-sample.pptx
fetch "$MS/testPPT_various.pptx"                      tika-various.pptx
fetch "$MISC/testOpenOffice2.odt"                     tika-sample.odt
fetch "$MISC/versions/LibreOfficeCalc_ods_1.3.ods"    tika-sample.ods
fetch "$MISC/versions/LibreOfficeImpress_odp_1.3.odp" tika-sample.odp
fetch "$PDF/testPDF.pdf"                              tika-sample.pdf
fetch "$XMP/testJPEG_commented_pspcs2mac.jpg"         tika-meta.jpg
fetch "$IMG/testJPEG.jpg"                             tika-plain.jpg
fetch "$IMG/testTIFF.tif"                             tika-meta.tiff
fetch "$TXT/testTXTNonASCIIUTF8.txt"                  tika-sample.txt

echo "Done."
