<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast\ValueCaster;

use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Transformer\Cast\ValueCaster;

/**
 * @psalm-immutable
 */
final class DateTimeToStringCaster implements ValueCaster
{
    private string $format;

    public function __construct(string $format = \DateTimeInterface::ATOM)
    {
        $this->format = $format;
    }

    public function cast($value) : string
    {
        if (!$value instanceof \DateTimeInterface) {
            throw new InvalidArgumentException('Only \DateTimeInterface can be casted to string, got ' . \gettype($value));
        }

        /** @psalm-suppress ImpureMethodCall */
        return $value->format($this->format);
    }
}
