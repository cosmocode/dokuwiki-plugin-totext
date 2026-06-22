<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum DirectionNameValue: string implements NameValue {
    case L2R = 'L2R';
    case R2L = 'R2L';
}
