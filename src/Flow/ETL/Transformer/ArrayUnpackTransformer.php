<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;
use Flow\ETL\Transformer\Factory\NativeEntryFactory;

/**
 * @psalm-immutable
 */
final class ArrayUnpackTransformer implements Transformer
{
    private string $arrayEntryName;

    private EntryFactory $entryFactory;

    public function __construct(string $arrayEntryName, EntryFactory $entryFactory = null)
    {
        $this->arrayEntryName = $arrayEntryName;
        $this->entryFactory = $entryFactory ? $entryFactory : new NativeEntryFactory();
    }

    /**
     * @psalm-suppress InvalidArgument
     * @psalm-suppress InvalidScalarArgument
     * @psalm-suppress MixedArgument
     */
    public function transform(Rows $rows) : Rows
    {
        return $rows->map(function (Row $row) : Row {
            if (!$row->entries()->has($this->arrayEntryName)) {
                throw new RuntimeException("\"{$this->arrayEntryName}\" not found");
            }

            if (!$row->entries()->get($this->arrayEntryName) instanceof Row\Entry\ArrayEntry) {
                throw new RuntimeException("\"{$this->arrayEntryName}\" is not ArrayEntry");
            }

            $entries = $row->entries()->remove($this->arrayEntryName);

            /**
             * @var int|string $key
             * @var mixed $value
             */
            foreach ($row->valueOf($this->arrayEntryName) as $key => $value) {
                $entryName = (string) $key;

                $entries = $entries->add($this->entryFactory->createEntry($entryName, $value));
            }

            return new Row($entries);
        });
    }
}
