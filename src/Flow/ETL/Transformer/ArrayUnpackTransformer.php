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

    /**
     * @var string[]
     */
    private array $skipEntryNames;

    private ?string $entryPrefix;

    private EntryFactory $entryFactory;

    /**
     * @param string[] $skipEntryNames
     */
    public function __construct(string $arrayEntryName, array $skipEntryNames = [], ?string $entryPrefix = null, EntryFactory $entryFactory = null)
    {
        $this->arrayEntryName = $arrayEntryName;
        $this->skipEntryNames = $skipEntryNames;
        $this->entryFactory = $entryFactory ?? new NativeEntryFactory();
        $this->entryPrefix = $entryPrefix;
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

            /**
             * @var int|string $key
             * @var mixed $value
             */
            foreach ($row->valueOf($this->arrayEntryName) as $key => $value) {
                $entryName = (string) $key;

                if (\in_array($entryName, $this->skipEntryNames, true)) {
                    continue;
                }

                if ($this->entryPrefix) {
                    $entryName = $this->entryPrefix . $entryName;
                }

                $row = $row->add($this->entryFactory->createEntry($entryName, $value));
            }

            return $row;
        });
    }
}
