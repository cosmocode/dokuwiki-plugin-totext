<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Extraction\TextGrouping\LineGrouping;

use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\PositionedTextElement;
use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\Vector;
use PrinsFrank\PdfParser\Extraction\TextGrouping\BaselineFrame;

/**
 *    #
 *   # #
 *  #####
 * #     #  #####  __< Baseline of "A" as being crossed by "Z", so will match depending on overlap percentage
 *             #       #   # __< Top of "Y" is below baseline of "A" so will never be considered
 *            #         ##
 *          #####       #
 *
 * Strategy where we sort all positioned text elements, retrieve the very first text element from the page (highest)
 * And for each text element check if there is significant overlap above a threshold. Continue until all elements are processed
 */
class TextOverlapStrategy {
    private const OVERLAP_RATIO = .9;

    /**
     * @param list<PositionedTextElement> $positionedTextElements
     * @param BaselineFrame $frame the axes to measure in; "highest" and "overlap" are measured across its baseline
     * @return iterable<list<PositionedTextElement>>
     */
    public static function group(array $positionedTextElements, BaselineFrame $frame = new BaselineFrame(new Vector(1.0, 0.0))): iterable {
        $acrossPositions = array_map($frame->across(...), $positionedTextElements);
        arsort($acrossPositions);
        $positionedTextElements = array_map(static fn(int $index) => $positionedTextElements[$index], array_keys($acrossPositions));
        $acrossPositions = array_values($acrossPositions);
        $alongPositions = array_map($frame->along(...), $positionedTextElements);

        /** @var array<int, true> $processedIndices */
        $processedIndices = [];
        $nrOfItems = count($positionedTextElements);
        for ($i = 0; $i < $nrOfItems; $i++) {
            if ($processedIndices[$i] ?? false) {
                continue;
            }

            $processedIndices[$i] = true;
            /** @var PositionedTextElement $highestPositionedTextElement */
            $highestPositionedTextElement = $positionedTextElements[$i];
            $positionedTextElementsOnLine = [$highestPositionedTextElement];
            $highestPositionedTextElementHeight = $highestPositionedTextElement->getHeight();
            if ($highestPositionedTextElementHeight === 0.0) {
                yield $positionedTextElementsOnLine;
                continue;
            }

            $highestPositionedTextElementBottom = $acrossPositions[$i];
            $highestElementTop = $highestPositionedTextElementBottom + $highestPositionedTextElementHeight;
            $lineLeftX = $lineRightX = $alongPositions[$i];
            for ($j = $i + 1; $j < $nrOfItems; $j++) {
                if ($processedIndices[$j] ?? false) {
                    continue;
                }

                $positionedTextElement = $positionedTextElements[$j];
                $currentElementBottom = $acrossPositions[$j];
                $currentHeight = $positionedTextElement->getHeight();
                $currentElementTop = $currentElementBottom + $currentHeight;
                if ($currentElementTop < $highestPositionedTextElementBottom) {
                    break;
                }

                if ($currentHeight === 0.0) {
                    continue;
                }

                $overlap = min($highestElementTop, $currentElementTop) - max($highestPositionedTextElementBottom, $currentElementBottom);
                if ($overlap <= 0.0) {
                    continue;
                }

                $offsetX = $alongPositions[$j];
                if ($overlap / min($currentHeight, $highestPositionedTextElementHeight) >= self::OVERLAP_RATIO
                    || ($currentHeight < $highestPositionedTextElementHeight && $offsetX >= $lineLeftX && $offsetX <= $lineRightX)) {
                    $positionedTextElementsOnLine[] = $positionedTextElement;
                    $processedIndices[$j] = true;
                    $lineLeftX = min($lineLeftX, $offsetX);
                    $lineRightX = max($lineRightX, $offsetX);
                }
            }

            usort(
                $positionedTextElementsOnLine,
                static fn(PositionedTextElement $a, PositionedTextElement $b): int => $frame->along($a) <=> $frame->along($b),
            );

            yield $positionedTextElementsOnLine;
        }
    }
}
