<?php

namespace dokuwiki\plugin\totext\test;

use splitbrain\PHPArchive\Zip;

/**
 * Builds tiny valid fixtures on disk for use in tests.
 *
 * Fixtures are intentionally minimal — they contain only the parts the
 * extractor reads, not everything required to open in Office/LibreOffice.
 */
final class FixtureBuilder
{
    /**
     * Create a fresh temporary directory.
     *
     * @return string the directory path
     */
    public static function tempDir()
    {
        $dir = sys_get_temp_dir() . '/totext-tests-' . bin2hex(random_bytes(8));
        mkdir($dir, 0700, true);
        return $dir;
    }

    /**
     * Recursively remove a directory and its contents.
     *
     * @param string $dir directory to remove
     * @return void
     */
    public static function cleanup($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );
        foreach ($it as $file) {
            $file->isDir() ? @rmdir($file->getPathname()) : @unlink($file->getPathname());
        }
        @rmdir($dir);
    }

    /**
     * Build a minimal DOCX file.
     *
     * @param string $outPath destination path
     * @return void
     */
    public static function buildDocx($outPath)
    {
        $contentTypes = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
</Types>
XML;
        $rels = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>
XML;
        $document = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
    <w:body>
        <w:p><w:r><w:t>Hello world from DOCX</w:t></w:r></w:p>
        <w:p><w:r><w:t xml:space="preserve">Tab</w:t><w:tab/><w:t>separated</w:t></w:r></w:p>
        <w:p><w:r><w:t>Line one</w:t><w:br/><w:t>line two</w:t></w:r></w:p>
    </w:body>
</w:document>
XML;
        self::writeZip($outPath, [
            '[Content_Types].xml' => $contentTypes,
            '_rels/.rels' => $rels,
            'word/document.xml' => $document,
        ]);
    }

    /**
     * Build a minimal XLSX file.
     *
     * @param string $outPath destination path
     * @return void
     */
    public static function buildXlsx($outPath)
    {
        $contentTypes = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
</Types>
XML;
        $rels = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>
XML;
        $workbook = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
          xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="Data" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>
XML;
        $shared = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="2" uniqueCount="2">
    <si><t>Hello</t></si>
    <si><t>World</t></si>
</sst>
XML;
        $sheet = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <sheetData>
        <row r="1">
            <c r="A1" t="s"><v>0</v></c>
            <c r="B1" t="s"><v>1</v></c>
        </row>
        <row r="2">
            <c r="A2"><v>42</v></c>
            <c r="B2" t="inlineStr"><is><t>inline</t></is></c>
        </row>
    </sheetData>
</worksheet>
XML;
        self::writeZip($outPath, [
            '[Content_Types].xml' => $contentTypes,
            '_rels/.rels' => $rels,
            'xl/workbook.xml' => $workbook,
            'xl/sharedStrings.xml' => $shared,
            'xl/worksheets/sheet1.xml' => $sheet,
        ]);
    }

    /**
     * Build a minimal PPTX file with two slides in reversed rels order.
     *
     * @param string $outPath destination path
     * @return void
     */
    public static function buildPptx($outPath)
    {
        $contentTypes = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/ppt/presentation.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.presentation.main+xml"/>
    <Override PartName="/ppt/slides/slide1.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slide+xml"/>
    <Override PartName="/ppt/slides/slide2.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slide+xml"/>
</Types>
XML;
        $rels = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="ppt/presentation.xml"/>
</Relationships>
XML;
        // Note: order rId2 then rId1 to verify the extractor follows sldIdLst order, not file/rels order.
        $presentation = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<p:presentation xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main"
                xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <p:sldIdLst>
        <p:sldId id="256" r:id="rId2"/>
        <p:sldId id="257" r:id="rId1"/>
    </p:sldIdLst>
</p:presentation>
XML;
        $presRels = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slide" Target="slides/slide2.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slide" Target="slides/slide1.xml"/>
</Relationships>
XML;
        $slide1 = self::pptxSlideXml('First slide title', 'First slide body');
        $slide2 = self::pptxSlideXml('Second slide', 'More content');

        self::writeZip($outPath, [
            '[Content_Types].xml' => $contentTypes,
            '_rels/.rels' => $rels,
            'ppt/presentation.xml' => $presentation,
            'ppt/_rels/presentation.xml.rels' => $presRels,
            'ppt/slides/slide1.xml' => $slide1,
            'ppt/slides/slide2.xml' => $slide2,
        ]);
    }

    /**
     * Build a single PPTX slide part.
     *
     * @param string $title slide title text
     * @param string $body slide body text
     * @return string slide XML
     */
    private static function pptxSlideXml($title, $body)
    {
        $titleEsc = htmlspecialchars($title, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $bodyEsc = htmlspecialchars($body, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<p:sld xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main"
       xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">
    <p:cSld><p:spTree>
        <p:sp><p:txBody>
            <a:p><a:r><a:t>$titleEsc</a:t></a:r></a:p>
            <a:p><a:r><a:t>$bodyEsc</a:t></a:r></a:p>
        </p:txBody></p:sp>
    </p:spTree></p:cSld>
</p:sld>
XML;
    }

    /**
     * Build a minimal ODT file.
     *
     * @param string $outPath destination path
     * @return void
     */
    public static function buildOdt($outPath)
    {
        $content = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<office:document-content
    xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
    xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0">
    <office:body><office:text>
        <text:h text:outline-level="1">Hello world from ODT</text:h>
        <text:p>First paragraph<text:tab/>after tab</text:p>
        <text:p>Line one<text:line-break/>line two</text:p>
    </office:text></office:body>
</office:document-content>
XML;
        self::writeOdf($outPath, 'application/vnd.oasis.opendocument.text', $content);
    }

    /**
     * Build a minimal ODS file.
     *
     * @param string $outPath destination path
     * @return void
     */
    public static function buildOds($outPath)
    {
        $content = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<office:document-content
    xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
    xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0"
    xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0">
    <office:body><office:spreadsheet>
        <table:table table:name="Data">
            <table:table-row>
                <table:table-cell><text:p>Hello</text:p></table:table-cell>
                <table:table-cell><text:p>World</text:p></table:table-cell>
            </table:table-row>
            <table:table-row>
                <table:table-cell><text:p>42</text:p></table:table-cell>
                <table:table-cell><text:p>inline</text:p></table:table-cell>
            </table:table-row>
        </table:table>
    </office:spreadsheet></office:body>
</office:document-content>
XML;
        self::writeOdf($outPath, 'application/vnd.oasis.opendocument.spreadsheet', $content);
    }

    /**
     * Build a minimal ODP file with two pages (slides).
     *
     * @param string $outPath destination path
     * @return void
     */
    public static function buildOdp($outPath)
    {
        $content = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<office:document-content
    xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
    xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0"
    xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0">
    <office:body><office:presentation>
        <draw:page draw:name="page1">
            <draw:frame><draw:text-box>
                <text:p>First slide title</text:p>
                <text:p>First slide body</text:p>
            </draw:text-box></draw:frame>
        </draw:page>
        <draw:page draw:name="page2">
            <draw:frame><draw:text-box>
                <text:p>Second slide</text:p>
            </draw:text-box></draw:frame>
        </draw:page>
    </office:presentation></office:body>
</office:document-content>
XML;
        self::writeOdf($outPath, 'application/vnd.oasis.opendocument.presentation', $content);
    }

    /**
     * Build a plain text file with the given content.
     *
     * @param string $outPath destination path
     * @param string $content file content
     * @return void
     */
    public static function buildTextFile($outPath, $content)
    {
        file_put_contents($outPath, $content);
    }

    /**
     * Build a minimal JPEG carrying IPTC metadata.
     *
     * @param string $outPath destination path
     * @return void
     */
    public static function buildJpeg($outPath)
    {
        $img = imagecreatetruecolor(8, 8);
        imagejpeg($img, $outPath);

        $meta = new \JpegMeta($outPath);
        $meta->setField('Iptc.Headline', 'Test Title');
        $meta->setField('Iptc.Caption', 'A caption describing the image');
        $meta->setField('Iptc.Byline', 'Jane Photographer');
        $meta->setField('Iptc.Keywords', 'alpha beta');
        $meta->setField('Iptc.CopyrightNotice', 'Copyright ACME');
        $meta->save();
    }

    /**
     * Build a minimal PDF containing the given text.
     *
     * @param string $outPath destination path
     * @param string $text text to embed
     * @return void
     */
    public static function buildPdf($outPath, $text = 'Hello PDF world')
    {
        $escaped = strtr($text, ['\\' => '\\\\', '(' => '\\(', ')' => '\\)']);
        $stream = "BT\n/F1 24 Tf\n100 700 Td\n($escaped) Tj\nET\n";

        $objs = [
            1 => "<< /Type /Catalog /Pages 2 0 R >>",
            2 => "<< /Type /Pages /Kids [3 0 R] /Count 1 >>",
            3 => "<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 4 0 R >> >> /MediaBox [0 0 612 792] /Contents 5 0 R >>",
            4 => "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>",
            5 => "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream",
        ];

        $body = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];
        foreach ($objs as $i => $content) {
            $offsets[$i] = strlen($body);
            $body .= "$i 0 obj\n$content\nendobj\n";
        }
        $xrefOffset = strlen($body);
        $count = count($objs) + 1;
        $body .= "xref\n0 $count\n0000000000 65535 f \n";
        foreach ($objs as $i => $_) {
            $body .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $body .= "trailer\n<< /Size $count /Root 1 0 R >>\nstartxref\n$xrefOffset\n%%EOF\n";

        file_put_contents($outPath, $body);
    }

    /**
     * Write an OpenDocument package (mimetype + content.xml) as a ZIP.
     *
     * @param string $outPath destination path
     * @param string $mimetype the ODF mimetype
     * @param string $content the content.xml body
     * @return void
     */
    private static function writeOdf($outPath, $mimetype, $content)
    {
        self::writeZip($outPath, [
            'mimetype' => $mimetype,
            'content.xml' => $content,
        ]);
    }

    /**
     * Write a ZIP archive from a map of internal path => content.
     *
     * @param string $outPath destination path
     * @param array<string, string> $entries internal path => content
     * @return void
     */
    private static function writeZip($outPath, array $entries)
    {
        $zip = new Zip();
        $zip->create($outPath);
        foreach ($entries as $name => $data) {
            $zip->addData($name, $data);
        }
        $zip->close();
    }
}
