<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Extraction\TextGrouping;

use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\PositionedTextElement;
use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\Vector;

/**
 * The axes of text with one baseline direction. Positions along the baseline take the role of page X, positions
 * across it take the role of page Y. The page frame has the baseline along page X.
 */
readonly class BaselineFrame {
    /** Unit vector perpendicular to the baseline, pointing from a line to the line above it */
    public Vector $normal;

    /** @param Vector $direction unit vector of the baseline (text advance) direction */
    public function __construct(
        public Vector $direction,
    ) {
        $this->normal = $direction->normal();
    }

    /** The frame of upright text: the baseline runs along page X. */
    public static function page(): self {
        return new self(new Vector(1.0, 0.0));
    }

    /** The position of the element's origin along the baseline. */
    public function along(PositionedTextElement $positionedTextElement): float {
        return $positionedTextElement->absoluteMatrix->offsetX * $this->direction->x
            + $positionedTextElement->absoluteMatrix->offsetY * $this->direction->y;
    }

    /** The position of the element's origin across the baseline. */
    public function across(PositionedTextElement $positionedTextElement): float {
        return $positionedTextElement->absoluteMatrix->offsetX * $this->normal->x
            + $positionedTextElement->absoluteMatrix->offsetY * $this->normal->y;
    }

    /** The position along the baseline where showing the element leaves the text cursor. */
    public function end(PositionedTextElement $positionedTextElement): float {
        return $this->along($positionedTextElement) + $positionedTextElement->getAdvanceWidth($this->direction);
    }
}
