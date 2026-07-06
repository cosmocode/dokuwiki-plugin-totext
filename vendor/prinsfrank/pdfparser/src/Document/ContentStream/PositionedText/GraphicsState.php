<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\ContentStream\PositionedText;

/**
 * The drawing state at one point while reading a content stream: where text lands on the page (the transformation
 * matrix) and how it looks (the text state: font, size and spacing). The PDF `q` and `Q` operators save and restore
 * exactly these two together, so the reader keeps a stack of GraphicsState values - one is pushed on `q` and popped
 * back on `Q`.
 *
 * Content reused through a Form XObject (painted with `Do`) starts from the state already in effect where it is
 * painted, not a blank one: it keeps the surrounding text state and combines its own /Matrix with the transformation
 * matrix. See ContentStream::getFormXObjectTextElements().
 *
 * @internal
 */
final readonly class GraphicsState {
    private function __construct(
        public TransformationMatrix $ctm,   // maps positions in the content stream to coordinates on the page
        public TextState $textState,        // font, size and spacing of the text being shown
    ) {}

    /** The state a content stream starts in: no transformation, and no font chosen yet. */
    public static function initial(): self {
        return new self(new TransformationMatrix(1, 0, 0, 1, 0, 0), new TextState(null, null));
    }

    public function withCtm(TransformationMatrix $ctm): self {
        return new self($ctm, $this->textState);
    }

    public function withTextState(TextState $textState): self {
        return new self($this->ctm, $textState);
    }
}
