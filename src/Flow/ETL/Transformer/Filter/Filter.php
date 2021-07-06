<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Filter;

use Flow\ETL\Row;

/**
 * @psalm-immutable
 */
interface Filter
{
    public function keep(Row $row) : bool;
}
