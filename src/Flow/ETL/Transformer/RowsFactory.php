<?php declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Rows;

interface RowsFactory
{
    /**
     * @return Rows
     */
    public function create() : Rows;
}
