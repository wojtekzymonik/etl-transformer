<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\ArrayEntry;

final class CastJsonToArray extends CastEntry
{
    public function __construct(string $entryName)
    {
        /**
         * @psalm-suppress MixedInferredReturnType
         * @psalm-suppress MixedReturnStatement
         */
        parent::__construct($entryName, ArrayEntry::class, [], fn (string $value) : array => \json_decode($value, true, JSON_THROW_ON_ERROR));
    }
}
