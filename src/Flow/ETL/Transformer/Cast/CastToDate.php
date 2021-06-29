<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Transformer\Cast\EntryCaster\StringToDateEntryCaster;

/**
 * @psalm-immutable
 */
final class CastToDate extends CastEntries
{
    /**
     * @param array<string> $entryNames
     * @param bool $nullable
     */
    public function __construct(array $entryNames, bool $nullable = false)
    {
        parent::__construct($entryNames, new StringToDateEntryCaster(), $nullable);
    }

    /**
     * @param array<string> $entryNames
     */
    public static function nullable(array $entryNames) : self
    {
        return new self($entryNames, true);
    }
}
