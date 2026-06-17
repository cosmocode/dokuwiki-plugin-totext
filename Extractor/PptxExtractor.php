<?php

namespace dokuwiki\plugin\totext\Extractor;

use dokuwiki\plugin\totext\Exception\ExtractionException;
use XMLReader;

/**
 * Extracts text from PowerPoint (.pptx) presentations.
 *
 * Slides are emitted in presentation order under "=== Slide N ===" headers,
 * each optionally followed by a "--- Notes ---" section.
 */
final class PptxExtractor extends AbstractZipXmlExtractor
{
    /** namespace URI for relationship references */
    private const RELS_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

    /** @inheritDoc */
    protected function extractText(): string
    {
        $slidePaths = $this->getSlideOrder();
        if ($slidePaths === []) {
            throw new ExtractionException('Not a valid PPTX file: no slides found');
        }

        $out = [];
        foreach ($slidePaths as $i => $slidePath) {
            $xml = $this->readPart($slidePath);
            if ($xml === null) {
                continue;
            }
            $body = $this->extractSlideText($xml);
            $out[] = "=== Slide " . ($i + 1) . " ===\n" . $body;

            $notesPath = $this->correspondingNotes($slidePath);
            if ($notesPath !== null) {
                $notesXml = $this->readPart($notesPath);
                if ($notesXml !== null) {
                    $notes = $this->extractSlideText($notesXml);
                    if (trim($notes) !== '') {
                        $out[] = "--- Notes ---\n" . $notes;
                    }
                }
            }
        }

        return trim(implode("\n", $out));
    }

    /**
     * Resolve slide order from presentation.xml + its relationships.
     *
     * @return string[] internal paths of slides in display order
     */
    private function getSlideOrder(): array
    {
        $relsXml = $this->readPart('ppt/_rels/presentation.xml.rels');
        $presXml = $this->readPart('ppt/presentation.xml');

        if ($relsXml === null || $presXml === null) {
            return $this->fallbackSlideOrder();
        }

        $rels = [];
        $reader = new XMLReader();
        if (!$reader->XML($relsXml, 'UTF-8', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            return $this->fallbackSlideOrder();
        }
        try {
            while ($reader->read()) {
                if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 'Relationship') {
                    $id = $reader->getAttribute('Id');
                    $target = $reader->getAttribute('Target');
                    if ($id !== null && $target !== null) {
                        $rels[$id] = $target;
                    }
                }
            }
        } finally {
            $reader->close();
        }

        $order = [];
        $reader = new XMLReader();
        if (!$reader->XML($presXml, 'UTF-8', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            return $this->fallbackSlideOrder();
        }
        try {
            while ($reader->read()) {
                if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 'sldId') {
                    $rid = $reader->getAttributeNs('id', self::RELS_NS);
                    if ($rid !== null && isset($rels[$rid])) {
                        $order[] = 'ppt/' . ltrim($rels[$rid], '/');
                    }
                }
            }
        } finally {
            $reader->close();
        }

        return $order !== [] ? $order : $this->fallbackSlideOrder();
    }

    /**
     * Fall back to filename order when the relationship graph is unusable.
     *
     * @return string[] internal slide paths
     */
    private function fallbackSlideOrder(): array
    {
        $slides = array_filter(
            $this->listParts('ppt/slides/slide'),
            fn($p) => str_ends_with($p, '.xml') && !str_contains($p, '_rels/'),
        );
        return array_values($slides);
    }

    /**
     * Find the notes slide that belongs to a given slide, if any.
     *
     * @param string $slidePath internal slide path
     * @return string|null internal notes path, or null if none
     */
    private function correspondingNotes(string $slidePath): ?string
    {
        if (!preg_match('#ppt/slides/slide(\d+)\.xml$#', $slidePath, $m)) {
            return null;
        }
        $notes = 'ppt/notesSlides/notesSlide' . $m[1] . '.xml';
        return $this->readPart($notes) !== null ? $notes : null;
    }

    /**
     * Walk the DrawingML text model of a slide or notes part.
     *
     * @param string $xml the part XML
     * @return string
     */
    private function extractSlideText(string $xml): string
    {
        return $this->extractTextFromXml(
            $xml,
            textElement: 't',
            blockElements: ['p', 'br'],
            tabElements: [],
        );
    }
}
