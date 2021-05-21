<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\JsonEntry;

final class CastToJson extends CastEntry
{
    private const JSON_DEPTH = 512;

    /**
     * @param array<string> $entryNames
     * @param bool $nullable
     *
     * @throws \Flow\ETL\Exception\InvalidArgumentException
     */
    public function __construct(array $entryNames, bool $nullable = false)
    {
        /**
         * @psalm-suppress MissingClosureParamType
         * @psalm-suppress MixedInferredReturnType
         * @psalm-suppress MixedReturnStatement
         */
        parent::__construct($entryNames, JsonEntry::class, [], $nullable, fn ($value) : array => \json_decode(\json_encode($value, JSON_THROW_ON_ERROR), true, self::JSON_DEPTH, JSON_THROW_ON_ERROR));
    }

    /**
     * @param array<string> $entryNames
     */
    public static function nullable(array $entryNames) : self
    {
        return new self($entryNames, true);
    }
}
