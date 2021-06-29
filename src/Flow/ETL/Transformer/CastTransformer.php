<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class CastTransformer implements Transformer
{
    /**
     * @var Cast\CastRow[]
     */
    private array $rowCasts;

    public function __construct(Transformer\Cast\CastRow ...$rowCasts)
    {
        $this->rowCasts = $rowCasts;
    }

    public function transform(Rows $rows) : Rows
    {
        /** @psalm-var pure-callable(Row $row) : Row $transformer */
        $transformer = function (Row $row) : Row {
            foreach ($this->rowCasts as $caster) {
                $row = $caster->cast($row);
            }

            return $row;
        };

        return $rows->map($transformer);
    }
}
