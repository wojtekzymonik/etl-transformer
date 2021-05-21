<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\ArrayEntry;

final class CastJsonToArray extends CastEntry
{
    /**
     * @param array<string> $entryNames
     */
    public function __construct(array $entryNames)
    {
        /**
         * @psalm-suppress MixedInferredReturnType
         * @psalm-suppress MixedReturnStatement
         */
        parent::__construct($entryNames, ArrayEntry::class, [], fn (string $value) : array => \json_decode($value, true, JSON_THROW_ON_ERROR));
    }
}
