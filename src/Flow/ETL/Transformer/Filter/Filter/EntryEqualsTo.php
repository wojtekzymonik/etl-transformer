<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Filter\Filter;

use Flow\ETL\Row;
use Flow\ETL\Transformer\Filter\Filter;

/**
 * @psalm-immutable
 */
final class EntryEqualsTo implements Filter
{
    private string $entryName;

    /**
     * @var mixed
     */
    private $entryValue;

    /**
     * @param string $entryName
     * @param mixed $entryValue
     */
    public function __construct(string $entryName, $entryValue)
    {
        $this->entryName = $entryName;
        $this->entryValue = $entryValue;
    }

    public function __invoke(Row $row) : bool
    {
        return $row->valueOf($this->entryName) === $this->entryValue;
    }
}
