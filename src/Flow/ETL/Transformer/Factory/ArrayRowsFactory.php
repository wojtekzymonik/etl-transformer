<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Factory;

use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\ArrayUnpackTransformer;
use Flow\ETL\Transformer\RowsFactory;

final class ArrayRowsFactory implements RowsFactory
{
    /**
     * @var array<array>
     */
    private array $data;

    /**
     * @param array<array> $data
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $data)
    {
        foreach ($data as $row) {
            /** @psalm-suppress DocblockTypeContradiction */
            if (!\is_array($row)) {
                throw new InvalidArgumentException('ArrayRowsFactory expects data to be an array of arrays');
            }
        }
        $this->data = $data;
    }

    public function create() : Rows
    {
        return (new ArrayUnpackTransformer('element'))->transform(new Rows(...\array_map(
            function (array $row) : Row {
                return Row::create(new Row\Entry\ArrayEntry('element', $row));
            },
            $this->data
        )));
    }
}
