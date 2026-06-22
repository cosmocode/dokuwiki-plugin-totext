<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum CFMNameValue: string implements NameValue {
    case None = 'None';
    case V2 = 'V2';
    case AESV2 = 'AESV2';
}
