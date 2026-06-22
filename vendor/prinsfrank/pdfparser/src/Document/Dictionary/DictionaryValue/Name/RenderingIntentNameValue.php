<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum RenderingIntentNameValue: string implements NameValue {
    case AbsoluteColorimetric = 'AbsoluteColorimetric';
    case RelativeColorimetric = 'RelativeColorimetric';
    case Saturation = 'Saturation';
    case Perceptual = 'Perceptual';
}
