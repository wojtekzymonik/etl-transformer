<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Row;
use Flow\ETL\Row\Entries;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class DynamicEntryTransformer implements Transformer
{
    /**
     * @var callable(Row) : Entries
     */
    private $generator;

    /**
     * @param callable(Row) : Entries $generator
     */
    public function __construct(callable $generator)
    {
        $this->generator = $generator;
    }

    public function transform(Rows $rows) : Rows
    {
        /**
         * @psalm-var pure-callable(Row) : Row $transformer
         */
        $transformer = function (Row $row) : Row {
            return new Row(new Entries(...\array_merge($row->entries()->all(), ($this->generator)($row)->all())));
        };

        return $rows->map($transformer);
    }
}
