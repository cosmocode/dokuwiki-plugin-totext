<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum NonFullScreenPageModeNameValue: string implements NameValue {
    case UseNone = 'UseNone';
    case UseOutlines = 'UseOutlines';
    case UseThumbs = 'UseThumbs';
    case UseOC = 'UseOC';
}
