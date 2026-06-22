<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum BorderStyleNameValue: string implements NameValue {
    case Solid = 'S';
    case Dashed = 'D';
    case Beveled = 'B';
    case Inset = 'I';
    case Underline = 'U';
}
