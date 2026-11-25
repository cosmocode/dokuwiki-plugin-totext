<?php declare(strict_types=1);

namespace PrinsFrank\MarkDownDom;

use PrinsFrank\MarkDownDom\Contract\BlockNode;
use PrinsFrank\MarkDownDom\Renderer\MarkdownRenderer;
use PrinsFrank\MarkDownDom\Renderer\TextRenderer;

readonly class Document {
    /** @var list<BlockNode> */
    public array $nodes;

    /** @no-named-arguments */
    public function __construct(
        BlockNode... $nodes,
    ) {
        $this->nodes = $nodes;
    }

    public function toMarkdown(): string {
        return (new MarkdownRenderer())
            ->render($this);
    }

    public function toText(): string {
        return (new TextRenderer())
            ->render($this);
    }
}
