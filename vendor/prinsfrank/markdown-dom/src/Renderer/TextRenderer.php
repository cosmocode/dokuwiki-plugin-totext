<?php declare(strict_types=1);

namespace PrinsFrank\MarkDownDom\Renderer;

use Exception;
use Override;
use PrinsFrank\MarkDownDom\Contract\Node;
use PrinsFrank\MarkDownDom\Node\Block\CodeBlock;
use PrinsFrank\MarkDownDom\Node\Block\Heading;
use PrinsFrank\MarkDownDom\Node\Block\Paragraph;
use PrinsFrank\MarkDownDom\Node\Block\Quote;
use PrinsFrank\MarkDownDom\Node\Block\Table\Table;
use PrinsFrank\MarkDownDom\Node\Block\Table\TableCell;
use PrinsFrank\MarkDownDom\Node\Block\Table\TableRow;
use PrinsFrank\MarkDownDom\Node\Block\ThematicBreak;
use PrinsFrank\MarkDownDom\Node\Inline\Bold;
use PrinsFrank\MarkDownDom\Node\Inline\CodeInline;
use PrinsFrank\MarkDownDom\Node\Inline\Image;
use PrinsFrank\MarkDownDom\Node\Inline\Italic;
use PrinsFrank\MarkDownDom\Node\Inline\Link;
use PrinsFrank\MarkDownDom\Node\Inline\Text;

class TextRenderer extends Renderer {
    /** @throws Exception */
    #[Override]
    protected function renderNode(Node $node): string {
        return match ($node::class) {
            Table::class => (fn(Table $table): string => (
                $table->headerRow !== null
                    ? $this->renderNode($table->headerRow) . PHP_EOL
                    : ''
            ) . $this->renderChildren($node->rows, "\n") . PHP_EOL)($node),
            TableCell::class => $this->renderChildren($node->children),
            TableRow::class => $this->renderChildren($node->cells, ' '),
            CodeBlock::class => $node->code . PHP_EOL,
            Heading::class => $this->renderChildren($node->children) . PHP_EOL,
            Paragraph::class => $this->renderChildren($node->children),
            Quote::class => $this->renderChildren($node->children) . PHP_EOL,
            ThematicBreak::class => PHP_EOL . PHP_EOL,
            Bold::class => $this->renderChildren($node->children),
            CodeInline::class => $node->code,
            Image::class => '',
            Italic::class => $this->renderChildren($node->children),
            Link::class => $node->href,
            Text::class => $node->content,
            default => throw new Exception(),
        };
    }
}
