<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum PageLayoutNameValue: string implements NameValue {
    case SinglePage = 'SinglePage';
    case OneColumn = 'OneColumn';
    case TwoColumnLeft = 'TwoColumnLeft';
    case TwoColumnRight = 'TwoColumnRight';
    case TwoPageLeft = 'TwoPageLeft';
    case TwoPageRight = 'TwoPageRight';
}
