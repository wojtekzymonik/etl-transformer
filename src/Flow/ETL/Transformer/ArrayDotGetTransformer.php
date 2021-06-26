<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use function Flow\ArrayDot\array_dot_exists;
use function Flow\ArrayDot\array_dot_get;
use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;
use Flow\ETL\Transformer\Factory\NativeEntryFactory;

/**
 * @psalm-immutable
 */
final class ArrayDotGetTransformer implements Transformer
{
    private string $arrayEntryName;

    private string $path;

    private bool $strictPath;

    private EntryFactory $entryFactory;

    public function __construct(
        string $arrayEntryName,
        string $path,
        bool $strictPath = true,
        EntryFactory $entryFactory = null
    ) {
        $this->arrayEntryName = $arrayEntryName;
        $this->path = $path;
        $this->strictPath = $strictPath;
        $this->entryFactory = $entryFactory ? $entryFactory : new NativeEntryFactory();
    }

    public function transform(Rows $rows) : Rows
    {
        /**
         * @psalm-var pure-callable(Row $row) : Row $transformer
         */
        $transformer = function (Row $row) : Row {
            $arrayEntry = $row->get($this->arrayEntryName);

            if (!$arrayEntry instanceof Row\Entry\ArrayEntry) {
                $entryClass = \get_class($arrayEntry);

                throw new RuntimeException("{$this->arrayEntryName} is not ArrayEntry but {$entryClass}");
            }

            if ($this->strictPath === false && !array_dot_exists($arrayEntry->value(), $this->path)) {
                return $row->add(new Row\Entry\NullEntry($this->path));
            }

            return $row->add(
                $this->entryFactory->createEntry(
                    $this->path,
                    array_dot_get($arrayEntry->value(), $this->path)
                )
            );
        };

        return $rows->map($transformer);
    }
}
