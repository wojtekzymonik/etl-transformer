<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Row;
use Flow\ETL\Row\Entry;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class StaticEntryTransformer implements Transformer
{
    /**
     * @var Entry
     */
    private Entry $entry;

    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    public function transform(Rows $rows) : Rows
    {
        /**
         * @psalm-var pure-callable(Row $row) : Row $transformer
         */
        $transformer = fn (Row $row) : Row => $row->add($this->entry);

        return $rows->map($transformer);
    }
}
