<?php declare(strict_types=1);

namespace PrinsFrank\MarkDownDom\Node\Block;

use PrinsFrank\MarkDownDom\Contract\BlockNode;
use PrinsFrank\MarkDownDom\Contract\InlineNode;

readonly class Paragraph implements BlockNode {
    /** @var list<InlineNode> */
    public array $children;

    /** @no-named-arguments */
    public function __construct(
        InlineNode... $children,
    ) {
        $this->children = $children;
    }
}
