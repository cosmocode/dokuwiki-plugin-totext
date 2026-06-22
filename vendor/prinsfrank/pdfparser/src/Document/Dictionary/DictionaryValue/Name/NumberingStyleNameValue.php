<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum NumberingStyleNameValue: string implements NameValue {
    case DecimalArabic = 'D';
    case UpperCaseRomanNumerals = 'R';
    case LowerCaseRomanNumerals = 'r';
    case UpperCaseLetters = 'A';
    case LowerCaseLetters = 'a';
}
