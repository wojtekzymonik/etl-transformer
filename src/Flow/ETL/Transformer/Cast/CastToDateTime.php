<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\DateTimeEntry;

final class CastToDateTime extends CastEntry
{
    public function __construct(string $entryName, string $format)
    {
        parent::__construct($entryName, DateTimeEntry::class, [$format], fn (string $value) : \DateTimeImmutable => new \DateTimeImmutable($value));
    }
}
