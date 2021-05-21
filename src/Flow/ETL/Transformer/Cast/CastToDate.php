<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\DateEntry;

final class CastToDate extends CastEntry
{
    /**
     * @param array<string> $entryNames
     */
    public function __construct(array $entryNames)
    {
        parent::__construct($entryNames, DateEntry::class, [], fn (string $value) : \DateTimeImmutable => new \DateTimeImmutable($value));
    }
}
