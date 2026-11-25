<?php declare(strict_types=1);

namespace PrinsFrank\MarkDownDom\Node\Block;

use PrinsFrank\MarkDownDom\Contract\BlockNode;

readonly class CodeBlock implements BlockNode {
    public function __construct(
        public string $code,
        public ?string $infoString,
    ) {}
}
