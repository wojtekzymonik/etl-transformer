<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast\ValueCaster;

use Flow\ETL\Transformer\Cast\ValueCaster;

/**
 * @psalm-immutable
 */
final class StringToDateCaster implements ValueCaster
{
    public function cast($value) : \DateTimeImmutable
    {
        return new \DateTimeImmutable((string) $value);
    }
}
