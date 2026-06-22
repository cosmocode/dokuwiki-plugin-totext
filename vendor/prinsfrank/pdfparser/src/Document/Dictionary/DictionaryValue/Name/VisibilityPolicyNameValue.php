<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum VisibilityPolicyNameValue: string implements NameValue {
    case AllOn = 'AllOn';
    case AnyOn = 'AnyOn';
    case AnyOff = 'AnyOff';
    case AllOff = 'AllOff';
}
