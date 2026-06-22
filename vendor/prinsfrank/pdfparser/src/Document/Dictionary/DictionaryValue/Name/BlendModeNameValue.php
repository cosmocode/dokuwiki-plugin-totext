<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum BlendModeNameValue: string implements NameValue {
    case Normal = 'Normal';
    case Compatible = 'Compatible';
    case Multiply = 'Multiply';
    case Screen = 'Screen';
    case Overlay = 'Overlay';
    case Darken = 'Darken';
    case Lighten = 'Lighten';
    case ColorDodge = 'ColorDodge';
    case ColorBurn = 'ColorBurn';
    case HardLight = 'HardLight';
    case SoftLight = 'SoftLight';
    case Difference = 'Difference';
    case Exclusion = 'Exclusion';
}
