<?php declare(strict_types=1);

namespace PrinsFrank\MarkDownDom\Node\Block\Table;

use PrinsFrank\MarkDownDom\Contract\BlockNode;

readonly class Table implements BlockNode {
    /** @var list<TableRow> */
    public array $rows;

    /** @no-named-arguments */
    public function __construct(
        public ?TableRow $headerRow,
        TableRow... $rows,
    ) {
        $this->rows = $rows;
    }
}
