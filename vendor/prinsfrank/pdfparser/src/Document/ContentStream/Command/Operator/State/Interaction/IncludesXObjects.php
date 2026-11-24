<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\ContentStream\Command\Operator\State\Interaction;

use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\GraphicsState;
use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\PositionedTextElement;
use PrinsFrank\PdfParser\Document\Dictionary\Decorator\Page;
use PrinsFrank\PdfParser\Document\Dictionary\Decorator\XObject;

interface IncludesXObjects {
    /**
     * @param GraphicsState $state the state in effect where the XObject is painted
     * @param list<int> $visitedObjectIds
     * @return list<PositionedTextElement>
     */
    public function getPositionedTextElements(string $operands, GraphicsState $state, Page|XObject $context, array $visitedObjectIds): array;
}
