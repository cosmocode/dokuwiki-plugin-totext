<?php declare(strict_types=1);

namespace PrinsFrank\Core14FontMetrics;

interface Font {
    public function getWidth(): float;

    public static function fromCharacterCode(int $code): ?self;
}
