<?php

namespace dokuwiki\plugin\totext\Extractor;

/**
 * Shared metadata reader for the OOXML family (DOCX/XLSX/PPTX).
 *
 * All three store their package metadata in the same two parts:
 * docProps/core.xml (Dublin Core + core properties) and docProps/app.xml
 * (the authoring application). The element local name => canonical key maps
 * are declared here once; subclasses only implement extractText().
 */
abstract class AbstractOoxmlExtractor extends AbstractZipXmlExtractor
{
    /**
     * docProps/core.xml element local name => canonical metadata key.
     *
     * @var array<string, string>
     */
    protected const CORE_META_MAP = [
        'title' => 'Title',
        'creator' => 'Author',
        'subject' => 'Subject',
        'keywords' => 'Keywords',
        'description' => 'Description',
        'created' => 'Created',
        'modified' => 'Modified',
        'language' => 'Language',
    ];

    /**
     * docProps/app.xml element local name => canonical metadata key.
     *
     * @var array<string, string>
     */
    protected const APP_META_MAP = [
        'Application' => 'Producer',
    ];

    /** @inheritDoc */
    protected function extractMetadata(): array
    {
        $meta = [];

        $core = $this->readPart('docProps/core.xml');
        if ($core !== null) {
            $meta = $this->mapMetadataFromXml($core, self::CORE_META_MAP);
        }

        $app = $this->readPart('docProps/app.xml');
        if ($app !== null) {
            $meta += $this->mapMetadataFromXml($app, self::APP_META_MAP);
        }

        return $meta;
    }
}
