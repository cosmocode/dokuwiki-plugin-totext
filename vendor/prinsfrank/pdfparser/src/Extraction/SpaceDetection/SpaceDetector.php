<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Extraction\SpaceDetection;

use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\PositionedTextElement;
use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\Vector;

class SpaceDetector {
    /** Detect a word break from the gap between two elements, measured along the baseline of the previous element. */
    public static function shouldContainExtraSpace(
        PositionedTextElement $positionedTextElement,
        PositionedTextElement $previousTextElementOnLine,
    ): bool {
        $baseline = $previousTextElementOnLine->absoluteMatrix->baselineVector();
        $baselineScale = $baseline->length();
        $direction = $baselineScale === 0.0 ? new Vector(1.0, 0.0) : $baseline->normalized();

        $gap = ($positionedTextElement->absoluteMatrix->offsetX - $previousTextElementOnLine->absoluteMatrix->offsetX) * $direction->x
            + ($positionedTextElement->absoluteMatrix->offsetY - $previousTextElementOnLine->absoluteMatrix->offsetY) * $direction->y
            - $previousTextElementOnLine->getAdvanceWidth($direction);

        $wordBreakThreshold = $previousTextElementOnLine->textState->getFontSize()
            * $baselineScale
            * ($previousTextElementOnLine->textState->scale / 100)
            * PositionedTextElement::WORD_BREAK_THRESHOLD_EM;

        return $gap >= $wordBreakThreshold;
    }
}
