<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum EventNameValue: string implements NameValue {
    case View = 'View';
    case Print = 'Print';
    case Export = 'Export';
}
