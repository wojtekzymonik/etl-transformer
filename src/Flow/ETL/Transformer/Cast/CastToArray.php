<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\ArrayEntry;

final class CastToArray extends CastEntry
{
    public function __construct(string $entryName)
    {
        /**
         * @psalm-suppress MissingClosureParamType
         */
        parent::__construct($entryName, ArrayEntry::class, [], fn ($value) : array => (array) $value);
    }
}
