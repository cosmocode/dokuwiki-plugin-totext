<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Extraction\TextGrouping;

use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\PositionedTextElement;
use PrinsFrank\PdfParser\Exception\PdfParserException;
use PrinsFrank\PdfParser\Extraction\TextGrouping\ColumnGrouping\ColumnGroupingStrategy;

/**
 * Groups text into lines in reading order at any rotation.
 *
 * Text elements are clustered by the direction of their baseline. Each cluster is grouped into lines and columns in
 * its own baseline frame, so rotated text reads along its baseline and does not break into one line per glyph. The
 * lines of each cluster are split into blocks, and the blocks of all clusters are merged by their highest point on
 * the page. This keeps a rotated overlay contiguous, so it does not interleave with the body text it crosses.
 */
class BaselineClusterStrategy {
    /** Maximum angle in degrees between two baselines that count as the same direction */
    private const ANGLE_TOLERANCE_DEGREES = 3.0;

    /** Gap between two lines, in line heights, that starts a new block */
    private const BLOCK_GAP_LINE_HEIGHTS = 2.5;

    /**
     * @param list<PositionedTextElement> $positionedTextElements
     * @throws PdfParserException
     * @return list<list<PositionedTextElement>>
     */
    public static function group(array $positionedTextElements): array {
        $clusters = self::clusterByBaseline($positionedTextElements);
        if (count($clusters) === 1) {
            return ColumnGroupingStrategy::group($clusters[0][1], $clusters[0][0]);
        }

        $documentOrder = [];
        foreach ($positionedTextElements as $index => $positionedTextElement) {
            $documentOrder[spl_object_id($positionedTextElement)] = $index;
        }

        $blocksPerCluster = [];
        foreach ($clusters as [$frame, $elementsInCluster]) {
            $blocksPerCluster[] = self::splitIntoBlocks(ColumnGroupingStrategy::group($elementsInCluster, $frame), $frame, $documentOrder);
        }

        $lines = [];
        foreach (self::mergeBlocks($blocksPerCluster) as $block) {
            array_push($lines, ...$block->lines);
        }

        return $lines;
    }

    /**
     * Cluster elements whose baselines point in the same direction. Directions close to page X use the page frame.
     *
     * @param list<PositionedTextElement> $positionedTextElements
     * @return list<array{BaselineFrame, list<PositionedTextElement>}>
     */
    private static function clusterByBaseline(array $positionedTextElements): array {
        $cosTolerance = cos(deg2rad(self::ANGLE_TOLERANCE_DEGREES));
        $pageFrame = BaselineFrame::page();
        $frames = $elementsPerFrame = [];
        foreach ($positionedTextElements as $positionedTextElement) {
            $baseline = $positionedTextElement->absoluteMatrix->baselineVector();
            $direction = $baseline->length() === 0.0 ? $pageFrame->direction : $baseline->normalized();
            foreach ($frames as $index => $frame) {
                if ($direction->dot($frame->direction) >= $cosTolerance) {
                    $elementsPerFrame[$index][] = $positionedTextElement;
                    continue 2;
                }
            }

            $frames[] = $direction->dot($pageFrame->direction) >= $cosTolerance ? $pageFrame : new BaselineFrame($direction);
            $elementsPerFrame[] = [$positionedTextElement];
        }

        $clusters = [];
        foreach ($frames as $index => $frame) {
            $clusters[] = [$frame, $elementsPerFrame[$index]];
        }

        return $clusters;
    }

    /**
     * Split lines in reading order into blocks. A block ends at a gap of more than BLOCK_GAP_LINE_HEIGHTS, or where
     * the next line lies above the previous one, as at the start of a new column.
     *
     * @param list<list<PositionedTextElement>> $lines
     * @param array<int, int> $documentOrder
     * @return list<Block>
     */
    private static function splitIntoBlocks(array $lines, BaselineFrame $frame, array $documentOrder): array {
        $blocks = $blockLines = [];
        $previousPosition = $previousHeight = null;
        foreach ($lines as $line) {
            $position = -INF;
            $height = 0.0;
            foreach ($line as $positionedTextElement) {
                $position = max($position, $frame->across($positionedTextElement));
                $height = max($height, $positionedTextElement->getHeight());
            }

            if ($previousPosition !== null
                && ($position > $previousPosition || $previousPosition - $position > max($previousHeight, $height) * self::BLOCK_GAP_LINE_HEIGHTS)) {
                $blocks[] = new Block($blockLines, $documentOrder);
                $blockLines = [];
            }

            $blockLines[] = $line;
            $previousPosition = $position;
            $previousHeight = $height;
        }

        if ($blockLines !== []) {
            $blocks[] = new Block($blockLines, $documentOrder);
        }

        return $blocks;
    }

    /**
     * Merge the blocks of all clusters. The order of the blocks within a cluster stays the same. Across clusters, the
     * next block is the one with the highest point on the page; ties keep document order.
     *
     * @param list<list<Block>> $blocksPerCluster
     * @return list<Block>
     */
    private static function mergeBlocks(array $blocksPerCluster): array {
        $merged = [];
        $nextIndices = array_fill(0, count($blocksPerCluster), 0);
        while (true) {
            $nextCluster = $nextBlock = null;
            foreach ($blocksPerCluster as $clusterIndex => $blocks) {
                $candidate = $blocks[$nextIndices[$clusterIndex]] ?? null;
                if ($candidate !== null
                    && ($nextBlock === null
                        || $candidate->top > $nextBlock->top
                        || ($candidate->top === $nextBlock->top && $candidate->documentPosition < $nextBlock->documentPosition))) {
                    $nextCluster = $clusterIndex;
                    $nextBlock = $candidate;
                }
            }

            if ($nextCluster === null || $nextBlock === null) {
                return $merged;
            }

            $merged[] = $nextBlock;
            $nextIndices[$nextCluster]++;
        }
    }
}
