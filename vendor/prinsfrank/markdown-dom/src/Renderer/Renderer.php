<?php declare(strict_types=1);

namespace PrinsFrank\MarkDownDom\Renderer;

use PrinsFrank\MarkDownDom\Contract\Node;
use PrinsFrank\MarkDownDom\Document;

abstract class Renderer {
    public function render(Document $document): string {
        $output = '';
        foreach ($document->nodes as $node) {
            $output .= $this->renderNode($node);
        }

        return $output;
    }

    /** @param list<Node> $nodes */
    protected function renderChildren(array $nodes, string $separator = ''): string {
        $output = '';
        foreach ($nodes as $index => $node) {
            if ($index !== 0) {
                $output .= $separator;
            }

            $output .= $this->renderNode($node);
        }

        return $output;
    }

    abstract protected function renderNode(Node $node): string;
}
