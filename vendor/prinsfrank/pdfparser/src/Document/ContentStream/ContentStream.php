<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\ContentStream;

use PrinsFrank\PdfParser\Document\ContentStream\Command\ContentStreamCommand;
use PrinsFrank\PdfParser\Document\ContentStream\Command\Operator\State\GraphicsStateOperator;
use PrinsFrank\PdfParser\Document\ContentStream\Command\Operator\State\Interaction\IncludesXObjects;
use PrinsFrank\PdfParser\Document\ContentStream\Command\Operator\State\Interaction\InteractsWithColorState;
use PrinsFrank\PdfParser\Document\ContentStream\Command\Operator\State\Interaction\InteractsWithTextState;
use PrinsFrank\PdfParser\Document\ContentStream\Command\Operator\State\Interaction\InteractsWithTransformationMatrix;
use PrinsFrank\PdfParser\Document\ContentStream\Command\Operator\State\Interaction\ProducesPositionedTextElements;
use PrinsFrank\PdfParser\Document\ContentStream\Object\TextObject;
use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\ColorState;
use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\GraphicsState;
use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\PositionedTextElement;
use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\TransformationMatrix;
use PrinsFrank\PdfParser\Document\Dictionary\Decorator\Page;
use PrinsFrank\PdfParser\Document\Dictionary\Decorator\XObject;
use PrinsFrank\PdfParser\Exception\ParseFailureException;

/** @api */
readonly class ContentStream {
    /** @var list<TextObject|ContentStreamCommand> */
    public array $content;

    /** @no-named-arguments */
    public function __construct(
        TextObject|ContentStreamCommand... $content,
    ) {
        $this->content = $content;
    }

    /**
     * @param GraphicsState $state the state the stream is painted in
     * @param list<int> $visitedObjectIds
     * @return list<PositionedTextElement>
     */
    public function getPositionedTextElements(Page|XObject $context, ?Page $parent, GraphicsState $state, array $visitedObjectIds): array {
        $positionedTextElements = $stack = [];
        $colorState = new ColorState();
        foreach ($this->content as $content) {
            if ($content instanceof ContentStreamCommand) {
                if ($content->operator === GraphicsStateOperator::SaveCurrentStateToStack) {
                    $stack[] = $state;
                } elseif ($content->operator === GraphicsStateOperator::RestoreMostRecentStateFromStack) {
                    $state = array_pop($stack) ?? throw new ParseFailureException();
                }

                if ($content->operator instanceof InteractsWithColorState) {
                    $colorState = $content->operator->applyToColorState($content->operands, $colorState);
                }

                if ($content->operator instanceof InteractsWithTextState) {
                    $state = $state->withTextState($content->operator->applyToTextState($content->operands, $state->textState, $context, $parent));
                }

                if ($content->operator instanceof InteractsWithTransformationMatrix) {
                    $state = $state->withCtm($content->operator->applyToTransformationMatrix($content->operands, $state->ctm, $state->textState));
                }

                if ($content->operator instanceof IncludesXObjects) {
                    $positionedTextElements = [...$positionedTextElements, ...$content->operator->getPositionedTextElements($content->operands, $state, $context, $visitedObjectIds)];
                }

                continue;
            }

            $textMatrix = new TransformationMatrix(1, 0, 0, 1, 0, 0); // Identity matrix, See Table 106, Tm operator for initial value in text object
            foreach ($content->contentStreamCommands as $contentStreamCommand) {
                if ($contentStreamCommand->operator instanceof InteractsWithColorState) {
                    $colorState = $contentStreamCommand->operator->applyToColorState($contentStreamCommand->operands, $colorState);
                }

                if ($contentStreamCommand->operator instanceof InteractsWithTextState) {
                    $state = $state->withTextState($contentStreamCommand->operator->applyToTextState($contentStreamCommand->operands, $state->textState, $context, $parent));
                }

                if ($contentStreamCommand->operator instanceof InteractsWithTransformationMatrix) {
                    $textMatrix = $contentStreamCommand->operator->applyToTransformationMatrix($contentStreamCommand->operands, $textMatrix, $state->textState);
                }

                if ($contentStreamCommand->operator instanceof ProducesPositionedTextElements) {
                    $positionedTextElements[] = $contentStreamCommand->operator->getPositionedTextElement($contentStreamCommand->operands, $textMatrix, $state->ctm, $state->textState, $colorState);
                }
            }
        }

        return $positionedTextElements;
    }
}
