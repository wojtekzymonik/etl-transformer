<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Filter\Filter;

use Flow\ETL\Row;
use Flow\ETL\Transformer\Filter\Filter;

/**
 * @psalm-immutable
 */
final class EntryExists implements Filter
{
    private string $entryName;

    /**
     * @param string $entryName
     */
    public function __construct(string $entryName)
    {
        $this->entryName = $entryName;
    }

    public function keep(Row $row) : bool
    {
        return $row->entries()->has($this->entryName);
    }
}
