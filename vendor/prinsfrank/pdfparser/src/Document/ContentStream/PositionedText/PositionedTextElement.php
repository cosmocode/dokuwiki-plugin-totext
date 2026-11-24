<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\ContentStream\PositionedText;

use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\TextSegment\TextSegment;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Rectangle\Rectangle;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

readonly class PositionedTextElement {
    private float $heightCache;
    private float $textSpaceAdvanceCache;
    private float $rightCache;
    public const WORD_BREAK_THRESHOLD_EM = 0.25;

    /** @param list<TextSegment> $textSegments */
    public function __construct(
        public array $textSegments,
        public TransformationMatrix $absoluteMatrix,
        public TextState $textState,
    ) {}

    /** @throws ParseFailureException */
    public function getText(): string {
        $text = '';
        foreach ($this->textSegments as $textSegment) {
            $textSegmentText = $textSegment->getText($this->textState->getFontOrThrow());
            if ($textSegment->offset !== null
                && $textSegment->offset / 1000 <= -self::WORD_BREAK_THRESHOLD_EM
                && str_ends_with($text, ' ') === false
                && str_starts_with($textSegmentText, ' ') === false) {
                $text .= ' ';
            }

            $text .= $textSegmentText;
        }

        return $text;
    }

    /** @return list<int> */
    public function getBytes(): array {
        $codePoints = [];
        foreach ($this->textSegments as $textSegment) {
            array_push($codePoints, ...$textSegment->textString?->getBytes() ?? []);
        }

        return $codePoints;
    }

    public function getHeight(): float {
        if (isset($this->heightCache)) {
            return $this->heightCache;
        }

        return $this->heightCache = ($this->textState->getFontSize())
            * hypot($this->absoluteMatrix->shearY, $this->absoluteMatrix->scaleY) // Length of the glyph's vertical axis, so rotated text keeps its height
            * ($this->textState->scale / 100);
    }

    /**
     * The distance, in device space, that showing this element advances the text cursor, per the displacement
     * formula in the PDF spec §9.4.4:
     *
     *   ((w0 − Tj/1000)·Tfs + Tc + Tw·[single-byte code 32]) · Th , transformed by the text rendering matrix.
     *
     * Reconstructed here because Tj/TJ do not advance the text matrix in this parser. The matrix turns the
     * horizontal text-space advance into a page-space vector along the baseline. The result is the signed
     * projection of that vector onto the unit vector $direction. The default direction is the page X axis.
     */
    public function getAdvanceWidth(Vector $direction = new Vector(1.0, 0.0)): float {
        if (isset($this->textSpaceAdvanceCache) === false) {
            $fontSize = $this->textState->getFontSize();

            $glyphAdvance = $this->textState->getFontOrThrow()->getWidthForBytes($this->getBytes(), $this->textState->wordSpace, $this->textState->charSpace, $fontSize); // Σ (w0·Tfs + Tc + Tw·[code 32])
            $offsetAdvance = -($this->getTotalOffset() / 1000) * $fontSize;                                                                                         // − Σ(Tj)/1000 · Tfs

            $this->textSpaceAdvanceCache = ($glyphAdvance + $offsetAdvance) * ($this->textState->scale / 100); // · Th
        }

        return $this->textSpaceAdvanceCache * $this->absoluteMatrix->baselineVector()->dot($direction);
    }

    public function getRight(): float {
        if (isset($this->rightCache)) {
            return $this->rightCache;
        }

        return $this->rightCache = $this->absoluteMatrix->offsetX + $this->getAdvanceWidth();
    }

    /** The sum of the TJ adjustment numbers in this element's segments, in thousandths of an em. */
    public function getTotalOffset(): float {
        $totalOffset = 0.0;
        foreach ($this->textSegments as $textSegment) {
            if ($textSegment->offset !== null) {
                $totalOffset += $textSegment->offset;
            }
        }

        return $totalOffset;
    }

    public function startsWithinRectangle(Rectangle $rectangle): bool {
        return $this->absoluteMatrix->offsetX <= $rectangle->xBottomRight
            && $this->absoluteMatrix->offsetX >= $rectangle->xTopLeft
            && $this->absoluteMatrix->offsetY >= $rectangle->yTopLeft
            && $this->absoluteMatrix->offsetY <= $rectangle->yBottomRight;
    }
}
