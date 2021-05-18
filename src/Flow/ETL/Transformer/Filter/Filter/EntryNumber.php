<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Filter\Filter;

use Flow\ETL\Row;
use Flow\ETL\Transformer\Filter\Filter;

/**
 * @psalm-immutable
 */
final class EntryNumber implements Filter
{
    private string $entryName;

    /**
     * @param string $entryName
     */
    public function __construct(string $entryName)
    {
        $this->entryName = $entryName;
    }

    public function __invoke(Row $row) : bool
    {
        return \is_numeric($row->get($this->entryName)->value());
    }
}
