<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\StringEntry;

final class CastToString extends CastEntry
{
    public function __construct(string $entryName)
    {
        /** @psalm-suppress MissingClosureParamType */
        parent::__construct($entryName, StringEntry::class, [], fn ($value) : string => (string) $value);
    }
}
