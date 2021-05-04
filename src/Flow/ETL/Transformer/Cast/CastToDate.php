<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\DateEntry;

final class CastToDate extends CastEntry
{
    public function __construct(string $entryName)
    {
        parent::__construct($entryName, DateEntry::class, [], fn (string $value) : \DateTimeImmutable => new \DateTimeImmutable($value));
    }
}
