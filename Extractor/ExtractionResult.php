<?php

namespace dokuwiki\plugin\totext\Extractor;

/**
 * Immutable result of a single extraction: the body text plus a normalised
 * metadata map, each with its own success/failure status.
 *
 * Every extractor produces both halves from one parse of the file. The metadata
 * keys are a single canonical vocabulary shared across all formats, so consumers
 * never have to special-case the source format:
 *
 *   Title, Author, Subject, Keywords, Description, Created, Modified,
 *   Language, Producer (all formats) plus Copyright (image-only).
 *
 * Values are non-empty UTF-8 strings; empty values are dropped by the
 * extractor rather than stored as blank keys.
 *
 * Text and metadata are extracted independently, so one half can fail while the
 * other succeeds (e.g. a document whose body part is missing but whose metadata
 * part is intact). A failed half carries its error in $textError / $metadataError
 * and the corresponding output is left empty; the salvaged half is still
 * returned. extract() only throws when *nothing* is recoverable (see the
 * extractors). An output that is empty *by design* — an image has no body text,
 * plain text has no metadata — is not a failure and leaves its error null.
 */
final class ExtractionResult
{
    /**
     * @param string $text the extracted body text ('' if absent or failed)
     * @param array<string, string> $metadata canonical key => value map
     * @param \Throwable|null $textError why text extraction failed, or null on success
     * @param \Throwable|null $metadataError why metadata extraction failed, or null on success
     */
    public function __construct(
        public readonly string $text,
        public readonly array $metadata = [],
        public readonly ?\Throwable $textError = null,
        public readonly ?\Throwable $metadataError = null,
    ) {
    }

    /**
     * Whether both halves were extracted without error.
     *
     * Note this reports the absence of *failure*, not the presence of content:
     * a result can be complete yet have empty text or empty metadata when the
     * file simply carried none.
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->textError === null && $this->metadataError === null;
    }
}
