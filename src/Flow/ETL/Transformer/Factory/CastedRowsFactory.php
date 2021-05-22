<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Factory;

use Flow\ETL\Rows;
use Flow\ETL\Transformer\Cast\CastEntry;
use Flow\ETL\Transformer\CastTransformer;
use Flow\ETL\Transformer\RowsFactory;

final class CastedRowsFactory implements RowsFactory
{
    private RowsFactory $factory;

    /**
     * @var array<CastEntry>
     */
    private array $castEntries;

    public function __construct(RowsFactory $factory, CastEntry ...$castEntries)
    {
        $this->factory = $factory;
        $this->castEntries = $castEntries;
    }

    public function create() : Rows
    {
        return (new CastTransformer(...$this->castEntries))->transform($this->factory->create());
    }
}
