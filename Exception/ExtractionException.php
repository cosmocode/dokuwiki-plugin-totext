<?php

namespace dokuwiki\plugin\totext\Exception;

/**
 * Thrown when text extraction fails due to an I/O or parse error.
 */
class ExtractionException extends \RuntimeException
{
    /**
     * Normalise a caught error to an ExtractionException.
     *
     * An error that is already an ExtractionException is returned unchanged so
     * its precise message survives; anything else is wrapped with the given
     * context and chained as the previous exception.
     *
     * @param \Throwable $previous the caught error
     * @param string $context context for the failure, e.g. "Failed to open /x.pdf"
     * @return self
     */
    public static function wrap(\Throwable $previous, string $context): self
    {
        if ($previous instanceof self) {
            return $previous;
        }
        return new self($context . ': ' . $previous->getMessage(), 0, $previous);
    }
}
