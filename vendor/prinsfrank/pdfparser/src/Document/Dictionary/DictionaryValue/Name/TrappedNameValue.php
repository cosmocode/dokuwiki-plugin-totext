<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum TrappedNameValue: string implements NameValue {
    case TRUE = 'True';
    case FALSE = 'False';
    case UNKNOWN = 'Unknown';
}
