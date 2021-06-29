<?php declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry;

/**
 * @psalm-immutable
 */
interface EntryCaster
{
    public function cast(Entry $entry) : Entry;
}
