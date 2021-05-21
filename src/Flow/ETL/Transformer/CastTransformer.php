<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class CastTransformer implements Transformer
{
    /**
     * @var Cast\CastEntry[]
     */
    private array $castEntries;

    public function __construct(Transformer\Cast\CastEntry ...$castEntries)
    {
        $this->castEntries = $castEntries;
    }

    /**
     * @psalm-suppress MixedAssignment
     * @psalm-suppress InvalidStringClass
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function transform(Rows $rows) : Rows
    {
        foreach ($this->castEntries as $castEntry) {
            foreach ($castEntry->entryNames() as $entryName) {
                $rows = $rows->map(
                    function (Row $row) use ($castEntry, $entryName) : Row {
                        if ($row->entries()->has($entryName)) {
                            $entry = $row->entries()->get($entryName);
                            $newEntryClass = $castEntry->newClass();
                            $castCallback = $castEntry->cast();

                            $newValue = ($castCallback) ? $castCallback($entry->value()) : $entry->value();

                            return (new Row($row->entries()->remove($entry->name())))->add(
                                new $newEntryClass($entry->name(), $newValue, ...$castEntry->extraArguments())
                            );
                        }

                        return $row;
                    }
                );
            }
        }

        return $rows;
    }
}
