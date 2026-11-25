<?php declare(strict_types=1);

namespace PrinsFrank\MarkDownDom\Node\Block\Table;

use PrinsFrank\MarkDownDom\Contract\BlockNode;

readonly class TableRow implements BlockNode {
    /** @var list<TableCell> */
    public array $cells;

    /** @no-named-arguments */
    public function __construct(
        TableCell... $cells,
    ) {
        $this->cells = $cells;
    }
}
