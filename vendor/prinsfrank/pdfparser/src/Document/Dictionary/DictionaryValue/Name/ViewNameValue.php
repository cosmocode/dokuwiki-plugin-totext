<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum ViewNameValue: string implements NameValue {
    case Details = 'D';
    case Tile = 'T';
    case Hidden = 'H';
}
