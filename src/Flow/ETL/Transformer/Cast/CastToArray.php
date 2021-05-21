<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\ArrayEntry;

final class CastToArray extends CastEntry
{
    /**
     * @param array<string> $entryNames
     */
    public function __construct(array $entryNames)
    {
        /**
         * @psalm-suppress MissingClosureParamType
         */
        parent::__construct($entryNames, ArrayEntry::class, [], fn ($value) : array => (array) $value);
    }
}
