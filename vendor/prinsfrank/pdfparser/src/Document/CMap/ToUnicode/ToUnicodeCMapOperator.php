<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\CMap\ToUnicode;

enum ToUnicodeCMapOperator: string {
    case BeginCodeSpaceRange = 'begincodespacerange';
    case EndCodeSpaceRange = 'endcodespacerange';
    case BeginBFChar = 'beginbfchar';
    case EndBFChar = 'endbfchar';
    case BeginBFRange = 'beginbfrange';
    case EndBFRange = 'endbfrange';
}
