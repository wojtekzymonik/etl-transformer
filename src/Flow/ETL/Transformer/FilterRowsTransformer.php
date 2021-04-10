<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;
use Flow\ETL\Transformer\Filter\Filter;

/**
 * @psalm-immutable
 */
final class FilterRowsTransformer implements Transformer
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
