<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class ArrayMergeTransformer implements Transformer
{
    /**
     * @var array<string>
     */
    private array $arrayEntries;

    private string $newEntryName;

    /**
     * @param array<string> $arrayEntries
     * @param string $newEntryName
     */
    public function __construct(array $arrayEntries, string $newEntryName = 'merged')
    {
        $this->arrayEntries = $arrayEntries;
        $this->newEntryName = $newEntryName;
    }

    public function transform(Rows $rows) : Rows
    {
        /**
         * @psalm-var pure-callable(Row $row) : Row $transformer
         */
        $transformer = function (Row $row) : Row {
            $entryValues = [];

            foreach ($this->arrayEntries as $entryName) {
                if (!$row->entries()->has($entryName)) {
                    throw new RuntimeException("\"{$entryName}\" not found");
                }

                if (!$row->entries()->get($entryName) instanceof Row\Entry\ArrayEntry) {
                    throw new RuntimeException("\"{$entryName}\" is not ArrayEntry");
                }

                /** @psalm-suppress MixedAssignment */
                $entryValues[] = $row->get($entryName)->value();
            }

            /** @psalm-suppress MixedArgument */
            return $row->add(new Row\Entry\ArrayEntry(
                $this->newEntryName,
                \array_merge(...$entryValues)
            ));
        };

        return $rows->map($transformer);
    }
}
