<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\ContentStream\PositionedText;

/**
 * The part of the graphics state that places and shapes text: the current transformation matrix and the text state
 * (font, size and spacing). The q operator saves it on a stack and the Q operator restores it.
 *
 * @internal
 */
final readonly class GraphicsState {
    private function __construct(
        public TransformationMatrix $ctm,   // maps positions in the content stream to coordinates on the page
        public TextState $textState,        // font, size and spacing of the text being shown
    ) {}

    /**
     * The state a content stream starts in: painted under $ctm - the matrix in effect where the stream is placed on
     * the page - and with no font chosen yet.
     */
    public static function initial(TransformationMatrix $ctm): self {
        return new self($ctm, new TextState(null, null));
    }

    public function withCtm(TransformationMatrix $ctm): self {
        return new self($ctm, $this->textState);
    }

    public function withTextState(TextState $textState): self {
        return new self($this->ctm, $textState);
    }
}
