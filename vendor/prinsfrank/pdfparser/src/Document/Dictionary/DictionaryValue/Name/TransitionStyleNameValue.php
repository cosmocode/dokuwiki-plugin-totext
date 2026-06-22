<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum TransitionStyleNameValue: string implements NameValue {
    case Split = 'Split';
    case Blinds = 'Blinds';
    case Box = 'Box';
    case Wipe = 'Wipe';
    case Dissolve = 'Dissolve';
    case Glitter = 'Glitter';
    case R = 'R';
    case Fly = 'Fly';
    case Push = 'Push';
    case Cover = 'Cover';
    case Uncover = 'Uncover';
    case Fade = 'Fade';
}
