<?php declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Rows;

interface RowsFactory
{
    /**
     * @param array<array> $data
     *
     * @return Rows
     */
    public function create(array $data) : Rows;
}
