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

class MarkdownRenderer extends Renderer {
    /** @throws Exception */
    #[Override]
    protected function renderNode(Node $node): string {
        return match ($node::class) {
            Table::class => (fn(Table $table): string
                => (
                    $table->headerRow !== null
                    ? ($this->renderNode($table->headerRow) . '|' . str_repeat(' --- |', count($table->headerRow->cells)) . PHP_EOL)
                    : ''
                ) . $this->renderChildren($node->rows) . PHP_EOL)($node),
            TableCell::class => $this->renderChildren($node->children),
            TableRow::class => '| ' . implode(' | ', array_map(fn(TableCell $cell) => $this->renderNode($cell), $node->cells)) . ' |' . PHP_EOL,
            CodeBlock::class => '```' . ($node->infoString ?? '') . PHP_EOL . $node->code . PHP_EOL . '```' . PHP_EOL,
            Heading::class => str_repeat('#', $node->level->value) . ' ' . $this->renderChildren($node->children) . PHP_EOL . PHP_EOL,
            Paragraph::class => $this->renderChildren($node->children),
            Quote::class => '> ' . str_replace("\n", "\n> ", $this->renderChildren($node->children)) . PHP_EOL,
            ThematicBreak::class => '---' . PHP_EOL,
            Bold::class => '**' . $this->renderChildren($node->children) . '**',
            CodeInline::class => '`' . $node->code . '`',
            Image::class => sprintf('![%s](%s)', $node->text, $node->href),
            Italic::class => '*' . $this->renderChildren($node->children) . '*',
            Link::class => sprintf('[%s](%s)', $node->text, $node->href),
            Text::class => $node->content,
            default => throw new Exception(),
        };
    }
}
