<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class KeepEntriesTransformer implements Transformer
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
        /** @psalm-suppress InvalidArgument */
        return $rows->map(function (Row $row) : Row {
            $entries = [];

            foreach ($row->entries()->all() as $entry) {
                if (\in_array($entry->name(), $this->names, true)) {
                    $entries[] = $entry;
                }
            }

            return new Row(new Row\Entries(...$entries));
        });
    }
}
