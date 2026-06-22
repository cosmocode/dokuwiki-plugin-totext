<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum IntentNameValue: string implements NameValue {
    case All = 'All';
    case View = 'View';
    case Design = 'Design';
}
