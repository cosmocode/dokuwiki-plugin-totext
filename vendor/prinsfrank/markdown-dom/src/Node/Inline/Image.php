<?php declare(strict_types=1);

namespace PrinsFrank\MarkDownDom\Node\Inline;

use PrinsFrank\MarkDownDom\Contract\InlineNode;

readonly class Image implements InlineNode {
    public function __construct(
        public string $text,
        public string $href,
    ) {}
}
