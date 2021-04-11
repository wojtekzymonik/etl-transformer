<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Filter\Filter;

use Flow\ETL\Row;
use Flow\ETL\Transformer\Filter\Filter;

/**
 * @psalm-immutable
 */
final class Callback implements Filter
{
    /**
     * @var callable(Row) : bool
     */
    private $callback;

    /**
     * @param callable(Row $row) : bool $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function __invoke(Row $row) : bool
    {
        return ($this->callback)($row);
    }
}
