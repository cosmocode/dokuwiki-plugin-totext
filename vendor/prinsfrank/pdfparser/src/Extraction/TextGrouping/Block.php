<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Extraction\TextGrouping;

use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\PositionedTextElement;

/** Consecutive lines of one orientation that read as one unit, for example a paragraph or a part of a column. */
readonly class Block {
    /** The highest point on the page (largest offsetY) of any element in the block */
    public float $top;

    /** The earliest content stream position of any element in the block */
    public int $documentPosition;

    /**
     * @param list<list<PositionedTextElement>> $lines in reading order
     * @param array<int, int> $documentOrder content stream position by spl_object_id() of the element
     */
    public function __construct(
        public array $lines,
        array $documentOrder,
    ) {
        $top = -INF;
        $documentPosition = PHP_INT_MAX;
        foreach ($lines as $line) {
            foreach ($line as $positionedTextElement) {
                $top = max($top, $positionedTextElement->absoluteMatrix->offsetY);
                $documentPosition = min($documentPosition, $documentOrder[spl_object_id($positionedTextElement)]);
            }
        }

        $this->top = $top;
        $this->documentPosition = $documentPosition;
    }
}
