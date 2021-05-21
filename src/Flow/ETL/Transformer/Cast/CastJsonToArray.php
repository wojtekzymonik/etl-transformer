<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\ArrayEntry;

final class CastJsonToArray extends CastEntry
{
    /**
     * @param array<string> $entryNames
     * @param bool $nullable
     *
     * @throws \Flow\ETL\Exception\InvalidArgumentException
     */
    public function __construct(array $entryNames, bool $nullable = false)
    {
        /**
         * @psalm-suppress MixedInferredReturnType
         * @psalm-suppress MixedReturnStatement
         */
        parent::__construct($entryNames, ArrayEntry::class, [], $nullable, fn (string $value) : array => \json_decode($value, true, JSON_THROW_ON_ERROR));
    }

    /**
     * @param array<string> $entryNames
     */
    public static function nullable(array $entryNames) : self
    {
        return new self($entryNames, true);
    }
}
