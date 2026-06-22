<?php

namespace dokuwiki\plugin\totext\Extractor;

/**
 * Shared metadata reader for the OpenDocument family (ODT/ODS/ODP).
 *
 * All three store their document metadata in a single meta.xml part inside
 * <office:meta>. The element local name => canonical key map is declared here
 * once; subclasses only implement extractText(). The meta:keyword element may
 * appear multiple times, so Keywords accumulates.
 */
abstract class AbstractOdfExtractor extends AbstractZipXmlExtractor
{
    /**
     * meta.xml element local name => canonical metadata key.
     *
     * @var array<string, string>
     */
    protected const META_MAP = [
        'title' => 'Title',
        'creator' => 'Author',
        'subject' => 'Subject',
        'keyword' => 'Keywords',
        'description' => 'Description',
        'creation-date' => 'Created',
        'date' => 'Modified',
        'language' => 'Language',
        'generator' => 'Producer',
    ];

    /** @inheritDoc */
    protected function extractMetadata(): array
    {
        $meta = $this->readPart('meta.xml');
        if ($meta === null) {
            return [];
        }
        return $this->mapMetadataFromXml($meta, self::META_MAP, ['Keywords']);
    }
}
