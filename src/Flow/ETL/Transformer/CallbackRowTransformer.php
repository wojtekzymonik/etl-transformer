<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class CallbackRowTransformer implements Transformer
{
    /**
     * @psalm-var pure-callable(Row) : Row
     * @phpstan-var callable(Row) : Row
     */
    private $callable;

    /**
     * @psalm-param pure-callable(Row) : Row $callable
     *
     * @param callable(Row) : Row $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function transform(Rows $rows) : Rows
    {
        return $rows->map($this->callable);
    }
}
