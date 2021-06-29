<?php declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

/**
 * @psalm-immutable
 */
interface ValueCaster
{
    /**
     * @psalm-pure
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function cast($value);
}
