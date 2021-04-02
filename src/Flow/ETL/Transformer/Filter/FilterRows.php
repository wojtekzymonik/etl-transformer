<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Filter;

use Flow\ETL\Row;
use Flow\ETL\Rows;

/**
 * @psalm-immutable
 */
final class FilterRows
{
    /**
     * @var Filter[]
     */
    private array $filters;

    public function __construct(Filter ...$filters)
    {
        $this->filters = $filters;
    }

    /** @psalm-suppress InvalidArgument */
    public function transform(Rows $rows) : Rows
    {
        return $rows->filter(
            function (Row $row) {
                foreach ($this->filters as $filter) {
                    if (false === $filter($row)) {
                        return false;
                    }
                }

                return true;
            }
        );
    }
}
