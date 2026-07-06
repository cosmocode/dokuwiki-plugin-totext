<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary;

use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\ExtendedDictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Document;

/**
 * The /Resources dictionaries in scope for a content stream, nearest scope first, against which the names appearing in
 * the stream (/F4, /Fm1, ...) are resolved: the first dictionary that defines the name wins, so an inner scope shadows
 * the outer ones.
 *
 * @internal
 */
final readonly class ResourceDictionaryChain {
    /** @param list<Dictionary> $dictionaries Nearest scope first; empty when no document is available, where nothing resolves */
    public function __construct(
        private array $dictionaries,
    ) {}

    public function prepend(Dictionary $dictionary): self {
        return new self([$dictionary, ...$this->dictionaries]);
    }

    public function resolve(?Document $document, DictionaryKey $resourceType, DictionaryKey|ExtendedDictionaryKey $name): ?ReferenceValue {
        foreach ($this->dictionaries as $dictionary) {
            $reference = $dictionary->getSubDictionary($document, $resourceType)
                ?->getValueForKey($document, $name, ReferenceValue::class);
            if ($reference instanceof ReferenceValue) {
                return $reference;
            }
        }

        return null;
    }
}
