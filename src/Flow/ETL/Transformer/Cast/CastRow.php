<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row;

/**
 * @psalm-immutable
 */
interface CastRow
{
    public function cast(Row $row) : Row;
}
