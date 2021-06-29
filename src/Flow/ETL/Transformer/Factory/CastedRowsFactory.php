<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Factory;

use Flow\ETL\Rows;
use Flow\ETL\Transformer\Cast\CastRow;
use Flow\ETL\Transformer\CastTransformer;
use Flow\ETL\Transformer\RowsFactory;

final class CastedRowsFactory implements RowsFactory
{
    private RowsFactory $factory;

    /**
     * @var array<CastRow>
     */
    private array $castEntries;

    public function __construct(RowsFactory $factory, CastRow ...$castEntries)
    {
        $this->factory = $factory;
        $this->castEntries = $castEntries;
    }

    public function create(array $data) : Rows
    {
        return (new CastTransformer(...$this->castEntries))->transform($this->factory->create($data));
    }
}
