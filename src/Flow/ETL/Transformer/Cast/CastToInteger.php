<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\IntegerEntry;

final class CastToInteger extends CastEntry
{
    /**
     * @param array<string> $entryNames
     * @param bool $nullable
     *
     * @throws \Flow\ETL\Exception\InvalidArgumentException
     */
    public function __construct(array $entryNames, bool $nullable = false)
    {
        /** @psalm-suppress MissingClosureParamType */
        parent::__construct($entryNames, IntegerEntry::class, [], $nullable, fn ($value) : int => (int) $value);
    }

    /**
     * @param array<string> $entryNames
     */
    public static function nullable(array $entryNames) : self
    {
        return new self($entryNames, true);
    }
}
