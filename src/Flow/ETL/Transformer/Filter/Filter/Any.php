<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Filter\Filter;

use Flow\ETL\Row;
use Flow\ETL\Transformer\Filter\Filter;

/**
 * @psalm-immutable
 */
final class Any implements Filter
{
    /**
     * @var Filter[]
     */
    private array $filter;

    public function __construct(Filter ...$filter)
    {
        $this->filter = $filter;
    }

    public function keep(Row $row) : bool
    {
        foreach ($this->filter as $filter) {
            if ($filter->keep($row)) {
                return true;
            }
        }

        return false;
    }
}
