<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Row\Entries;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;
use Flow\ETL\Transformer\Factory\NativeEntryFactory;

/**
 * @psalm-immutable
 */
final class ArrayExpandTransformer implements Transformer
{
    private string $arrayEntryName;

    private string $expandEntryName;

    private EntryFactory $entryFactory;

    public function __construct(string $arrayEntryName, string $expandEntryName = 'element', EntryFactory $entryFactory = null)
    {
        $this->arrayEntryName = $arrayEntryName;
        $this->expandEntryName = $expandEntryName;
        $this->entryFactory = $entryFactory ? $entryFactory : new NativeEntryFactory();
    }

    public function transform(Rows $rows) : Rows
    {
        /**
         * @psalm-var pure-callable(Row $row) : Row[] $transformer
         */
        $transformer = function (Row $row) : array {
            $arrayEntry = $row->get($this->arrayEntryName);

            if (!$arrayEntry instanceof Row\Entry\ArrayEntry) {
                $entryClass = \get_class($arrayEntry);

                throw new RuntimeException("{$this->arrayEntryName} is not ArrayEntry but {$entryClass}");
            }

            $array = $arrayEntry->value();

            return \array_values(
                \array_map(
                    function ($arrayElement) use ($row) : Row {
                        return new Row(new Entries(
                            ...\array_merge(
                                $row->entries()->remove($this->arrayEntryName)->all(),
                                [$this->entryFactory->createEntry($this->expandEntryName, $arrayElement)]
                            )
                        ));
                    },
                    $array
                )
            );
        };

        return $rows->flatMap($transformer);
    }
}
