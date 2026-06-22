<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Dictionary\DictionaryValue\Name;

enum PaperHandlingNameValue: string implements NameValue {
    case Simplex = 'Simplex';
    case DuplexFlipShortEdge = 'DuplexFlipShortEdge';
    case DuplexFlipLongEdge = 'DuplexFlipLongEdge';
}
