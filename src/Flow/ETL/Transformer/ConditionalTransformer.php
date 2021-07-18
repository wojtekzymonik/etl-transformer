<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class ConditionalTransformer implements Transformer
{
    private Condition\RowCondition $condition;

    private Transformer $transformer;

    public function __construct(Transformer\Condition\RowCondition $condition, Transformer $transformer)
    {
        $this->condition = $condition;
        $this->transformer = $transformer;
    }

    public function transform(Rows $rows) : Rows
    {
        /**
         * @psalm-var pure-callable(Row $row) : array<Row> $transformer
         */
        $transformer = function (Row $row) : array {
            if ($this->condition->isMetFor($row)) {
                return (array) $this->transformer->transform(new Rows($row))->getIterator();
            }

            return [$row];
        };

        return $rows->flatMap($transformer);
    }
}
