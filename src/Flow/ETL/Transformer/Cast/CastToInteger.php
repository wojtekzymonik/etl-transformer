<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\IntegerEntry;

final class CastToInteger extends CastEntry
{
    public function __construct(string $entryName)
    {
        /** @psalm-suppress MissingClosureParamType */
        parent::__construct($entryName, IntegerEntry::class, [], fn ($value) : int => (int) $value);
    }
}
