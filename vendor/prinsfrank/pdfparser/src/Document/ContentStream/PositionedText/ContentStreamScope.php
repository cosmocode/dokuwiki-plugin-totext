<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\ContentStream\PositionedText;

use PrinsFrank\PdfParser\Document\Dictionary\Dictionary;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\ExtendedDictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Reference\ReferenceValue;
use PrinsFrank\PdfParser\Document\Dictionary\ResourceDictionaryChain;
use PrinsFrank\PdfParser\Document\Document;

/**
 * The context for walking a content stream that the stream itself does not carry: the document its named references
 * resolve against, the {@see ResourceDictionaryChain} those names are looked up in, and the object numbers of the Form
 * XObjects currently being painted. Unlike {@see GraphicsState} it stays constant for one stream, changing only when
 * the walk descends into a Form XObject.
 *
 * @internal
 */
final readonly class ContentStreamScope {
    /** @param list<int> $visited Object numbers of the Form XObjects currently being painted, to break reference cycles */
    private function __construct(
        public ?Document $document, // null when no document is available, so painted forms cannot be resolved and are skipped
        public ResourceDictionaryChain $resourceChain,
        public array $visited = [],
    ) {}

    public static function standalone(): self {
        return new self(null, new ResourceDictionaryChain([]), []);
    }

    public static function forPage(Document $document, ResourceDictionaryChain $resourceChain): self {
        return new self($document, $resourceChain, []);
    }

    /** The form's own /Resources is prepended onto the inherited chain, and its object number recorded to break a reference cycle if it paints itself. */
    public function forForm(?Dictionary $formResources, int $objectNumber): self {
        return new self(
            $this->document,
            $formResources === null ? $this->resourceChain : $this->resourceChain->prepend($formResources),
            [...$this->visited, $objectNumber],
        );
    }

    public function resolve(DictionaryKey $resourceType, DictionaryKey|ExtendedDictionaryKey $name): ?ReferenceValue {
        return $this->resourceChain->resolve($this->document, $resourceType, $name);
    }
}
