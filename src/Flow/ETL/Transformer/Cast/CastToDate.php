<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\DateEntry;

final class CastToDate extends CastEntry
{
    /**
     * @param array<string> $entryNames
     * @param bool $nullable
     *
     * @throws \Flow\ETL\Exception\InvalidArgumentException
     */
    public function __construct(array $entryNames, bool $nullable = false)
    {
        parent::__construct($entryNames, DateEntry::class, [], $nullable, fn (string $value) : \DateTimeImmutable => new \DateTimeImmutable($value));
    }

    /**
     * @param array<string> $entryNames
     */
    public static function nullable(array $entryNames) : self
    {
        return new self($entryNames, true);
    }
}
