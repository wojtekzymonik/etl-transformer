<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\IntegerEntry;

final class CastToInteger extends CastEntry
{
    /**
     * @param array<string> $entryNames
     */
    public function __construct(array $entryNames)
    {
        /** @psalm-suppress MissingClosureParamType */
        parent::__construct($entryNames, IntegerEntry::class, [], fn ($value) : int => (int) $value);
    }
}
