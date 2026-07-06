<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\ContentStream;

use PrinsFrank\PdfParser\Document\ContentStream\Command\ContentStreamCommand;
use PrinsFrank\PdfParser\Document\ContentStream\Command\Operator\State\GraphicsStateOperator;
use PrinsFrank\PdfParser\Document\ContentStream\Command\Operator\State\Interaction\InteractsWithTransformationMatrix;
use PrinsFrank\PdfParser\Document\ContentStream\Command\Operator\State\Interaction\InteractsWithTextState;
use PrinsFrank\PdfParser\Document\ContentStream\Command\Operator\State\Interaction\ProducesPositionedTextElements;
use PrinsFrank\PdfParser\Document\ContentStream\Command\Operator\State\XObjectOperator;
use PrinsFrank\PdfParser\Document\ContentStream\Object\TextObject;
use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\ContentStreamScope;
use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\GraphicsState;
use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\LineGroupingStrategy\LineGroupingStrategy;
use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\PositionedTextElement;
use PrinsFrank\PdfParser\Document\ContentStream\PositionedText\TransformationMatrix;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\DictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryKey\ExtendedDictionaryKey;
use PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Array\ArrayValue;
use PrinsFrank\PdfParser\Document\Dictionary\ResourceDictionaryChain;
use PrinsFrank\PdfParser\Document\Document;
use PrinsFrank\PdfParser\Document\Object\Decorator\Page;
use PrinsFrank\PdfParser\Document\Object\Decorator\XObject;
use PrinsFrank\PdfParser\Exception\ParseFailureException;
use PrinsFrank\PdfParser\Exception\PdfParserException;

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
     * Return every run of text shown in this content stream, each with its position on the page. Without a $document,
     * Form XObjects painted with the `Do` operator cannot be resolved and are skipped; only this stream's own text is
     * returned.
     *
     * @throws PdfParserException
     * @return list<PositionedTextElement>
     */
    public function getPositionedTextElements(?Document $document = null, ?ResourceDictionaryChain $resourceChain = null): array {
        return $this->walk(
            $document === null ? ContentStreamScope::standalone() : ContentStreamScope::forPage($document, $resourceChain ?? new ResourceDictionaryChain([])),
            GraphicsState::initial(),
        );
    }

    /**
     * @throws PdfParserException
     * @return list<PositionedTextElement>
     */
    private function walk(ContentStreamScope $scope, GraphicsState $state): array {
        // The resolution scope is constant for the whole stream, so it is stamped onto the text state once here and
        // carried unchanged through every later state change rather than threaded into each element; getFont() resolves
        // the font lazily against it after the walk.
        $state = $state->withTextState($state->textState->withResourceChain($scope->resourceChain));
        $positionedTextElements = $stack = [];
        foreach ($this->content as $content) {
            if ($content instanceof ContentStreamCommand) {
                if ($content->operator === GraphicsStateOperator::SaveCurrentStateToStack) {
                    $stack[] = $state;
                } elseif ($content->operator === GraphicsStateOperator::RestoreMostRecentStateFromStack) {
                    $state = array_pop($stack) ?? throw new ParseFailureException();
                }

                if ($content->operator instanceof InteractsWithTextState) {
                    $state = $state->withTextState($content->operator->applyToTextState($content->operands, $state->textState));
                }

                if ($content->operator instanceof InteractsWithTransformationMatrix) {
                    $state = $state->withCtm($content->operator->applyToTransformationMatrix($content->operands, $state->ctm, $state->textState));
                }

                if ($content->operator === XObjectOperator::Paint) {
                    foreach ($this->getFormXObjectTextElements($scope, $state, $content->operands) as $formElement) {
                        $positionedTextElements[] = $formElement;
                    }
                }

                continue;
            }

            $textMatrix = new TransformationMatrix(1, 0, 0, 1, 0, 0); // Identity matrix, See Table 106, Tm operator for initial value in text object
            foreach ($content->contentStreamCommands as $contentStreamCommand) {
                if ($contentStreamCommand->operator instanceof InteractsWithTextState) {
                    $state = $state->withTextState($contentStreamCommand->operator->applyToTextState($contentStreamCommand->operands, $state->textState));
                }

                if ($contentStreamCommand->operator instanceof InteractsWithTransformationMatrix) {
                    $textMatrix = $contentStreamCommand->operator->applyToTransformationMatrix($contentStreamCommand->operands, $textMatrix, $state->textState);
                }

                if ($contentStreamCommand->operator instanceof ProducesPositionedTextElements) {
                    $positionedTextElements[] = $contentStreamCommand->operator->getPositionedTextElement($contentStreamCommand->operands, $textMatrix, $state->ctm, $state->textState);
                }
            }
        }

        return $positionedTextElements;
    }

    /**
     * Look up the Form XObject painted by a `Do` operator and return the text inside it, positioned on the page using
     * the form's /Matrix on top of the current matrix. It inherits the graphics state in effect where it is painted,
     * not a blank one (ISO 32000-1:2008 §8.10.1), so text in a form that never sets its own font still resolves one.
     *
     * @throws PdfParserException
     * @return list<PositionedTextElement>
     */
    private function getFormXObjectTextElements(ContentStreamScope $scope, GraphicsState $state, string $operands): array {
        if (($document = $scope->document) === null) {
            return [];
        }

        $reference = $scope->resolve(DictionaryKey::XOBJECT, ExtendedDictionaryKey::fromKeyString($operands));
        if ($reference === null || in_array($reference->objectNumber, $scope->visited, true)) {
            return [];
        }

        $xObject = $document->getObject($reference->objectNumber, XObject::class);
        if ($xObject === null || !$xObject->isForm()) {
            return [];
        }

        $formResources = $xObject->getDictionary()->getSubDictionary($document, DictionaryKey::RESOURCES);
        $formMatrix = $this->getFormMatrix($document, $xObject) ?? new TransformationMatrix(1, 0, 0, 1, 0, 0);

        return ContentStreamParser::parse([$xObject])
            ->walk(
                $scope->forForm($formResources, $reference->objectNumber),
                $state->withCtm($formMatrix->multiplyWith($state->ctm)),
            );
    }

    /** @throws PdfParserException */
    private function getFormMatrix(Document $document, XObject $xObject): ?TransformationMatrix {
        $matrix = $xObject->getDictionary()->getValueForKey($document, DictionaryKey::MATRIX, ArrayValue::class);
        if (!$matrix instanceof ArrayValue || count($matrix->value) !== 6) {
            return null;
        }

        $values = [];
        foreach ($matrix->value as $value) {
            if (!is_int($value) && !is_string($value)) {
                return null;
            }

            $values[] = (float) $value;
        }

        return new TransformationMatrix($values[0], $values[1], $values[2], $values[3], $values[4], $values[5]);
    }

    /** @throws PdfParserException */
    public function getText(Document $document, Page $page, LineGroupingStrategy $lineGroupingStrategy): string {
        $text = '';
        $isFirstLine = true;
        $positionedTextElements = $this->getPositionedTextElements($document, $page->getResourceChain());
        foreach ($lineGroupingStrategy->group($positionedTextElements) as $positionedTextElementsForLine) {
            if (!$isFirstLine) {
                $text .= "\n";
            }

            $isFirstLine = false;
            $previousTextElementOnLine = null;
            foreach ($positionedTextElementsForLine as $positionedTextElement) {
                $elementText = $positionedTextElement->getText($document);
                if ($elementText === '') {
                    $previousTextElementOnLine = $positionedTextElement;
                    continue;
                }

                if (
                    $previousTextElementOnLine !== null
                    && $lineGroupingStrategy->requiresSpaceBetween($previousTextElementOnLine, $positionedTextElement, $document)
                    && str_ends_with($text, ' ') === false
                    && str_starts_with($elementText, ' ') === false
                ) {
                    $text .= ' ';
                }

                $text .= $elementText;
                $previousTextElementOnLine = $positionedTextElement;
            }
        }

        return $text;
    }
}
