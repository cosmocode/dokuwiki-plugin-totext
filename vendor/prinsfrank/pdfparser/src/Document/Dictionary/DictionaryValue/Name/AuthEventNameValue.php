<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum AuthEventNameValue: string implements NameValue {
    case DocOpen = 'DocOpen';
    case EFOpen = 'EFOpen';
}
