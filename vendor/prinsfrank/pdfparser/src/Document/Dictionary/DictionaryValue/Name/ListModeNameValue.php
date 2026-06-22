<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum ListModeNameValue: string implements NameValue {
    case AllPages = 'AllPages';
    case VisiblePages = 'VisiblePages';
}
