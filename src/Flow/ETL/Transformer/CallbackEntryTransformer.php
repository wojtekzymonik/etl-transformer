<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Row;
use Flow\ETL\Row\Entry;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class CallbackEntryTransformer implements Transformer
{
    /**
     * @psalm-var array<pure-callable(Entry) : Entry>
     * @phpstan-var array<callable(Entry) : Entry>
     */
    private array $callables;

    /**
     * @psalm-param pure-callable(Entry) : Entry ...$callables
     *
     * @param callable(Entry) : Entry ...$callables
     */
    public function __construct(callable ...$callables)
    {
        $this->callables = $callables;
    }

    public function transform(Rows $rows) : Rows
    {
        /**
         * @var callable(Row) : Row $transform
         * @psalm-var pure-callable(Row) : Row $transform
         */
        $transform = function (Row $row) : Row {
            $entries = $row->entries()->map(function (Row\Entry $entry) : Row\Entry {
                foreach ($this->callables as $callable) {
                    $entry = $callable($entry);
                }

                return $entry;
            });

            return new Row(new Row\Entries(...$entries));
        };

        return $rows->map($transform);
    }
}
