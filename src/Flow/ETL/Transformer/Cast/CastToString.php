<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\StringEntry;

final class CastToString extends CastEntry
{
    /**
     * @param array<string> $entryNames
     */
    public function __construct(array $entryNames)
    {
        /** @psalm-suppress MissingClosureParamType */
        parent::__construct($entryNames, StringEntry::class, [], fn ($value) : string => (string) $value);
    }
}
