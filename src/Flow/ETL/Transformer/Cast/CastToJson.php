<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\JsonEntry;

final class CastToJson extends CastEntry
{
    private const JSON_DEPTH = 512;

    public function __construct(string $entryName)
    {
        /**
         * @psalm-suppress MissingClosureParamType
         * @psalm-suppress MixedInferredReturnType
         * @psalm-suppress MixedReturnStatement
         */
        parent::__construct($entryName, JsonEntry::class, [], fn ($value) : array => \json_decode(\json_encode($value, JSON_THROW_ON_ERROR), true, self::JSON_DEPTH, JSON_THROW_ON_ERROR));
    }
}
