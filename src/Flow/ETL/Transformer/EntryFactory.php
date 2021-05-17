<?php declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Row\Entry;

interface EntryFactory
{
    /**
     * @param string $entryName
     * @param mixed $value
     *
     * @return Entry
     */
    public function createEntry(string $entryName, $value) : Entry;
}
