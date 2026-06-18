<?php

declare(strict_types=1);

namespace Smalot\PdfParser\Exception;

/**
 * This exception is thrown when memory usage during parsing reaches the
 * configured headroom limit. It allows the caller to abort gracefully
 * instead of running into an uncatchable PHP out-of-memory fatal error.
 *
 * @see \Smalot\PdfParser\Config::setMemoryLimitHeadroomPercent()
 */
class MemoryLimitException extends \Exception
{
}
