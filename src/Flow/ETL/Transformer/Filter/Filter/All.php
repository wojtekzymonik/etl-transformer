<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Filter\Filter;

use Flow\ETL\Row;
use Flow\ETL\Transformer\Filter\Filter;

/**
 * @psalm-immutable
 */
final class All implements Filter
{
    /**
     * @var Filter[]
     */
    private array $filter;

    public function __construct(Filter ...$filter)
    {
        $this->filter = $filter;
    }

    public function __invoke(Row $row) : bool
    {
        foreach ($this->filter as $filter) {
            if (!$filter($row)) {
                return false;
            }
        }

        return true;
    }
}
