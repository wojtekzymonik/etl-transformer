<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class RemoveEntriesTransformer implements Transformer
{
    /**
     * @var string[]
     */
    private array $names;

    public function __construct(string ...$names)
    {
        $this->names = $names;
    }

    public function transform(Rows $rows) : Rows
    {
        foreach ($this->names as $name) {
            $rows = $rows->map(function (Row $row) use ($name) : Row {
                return $row->remove($name);
            });
        }

        return $rows;
    }
}
