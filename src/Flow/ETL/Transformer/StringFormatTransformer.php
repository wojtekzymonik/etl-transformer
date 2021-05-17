<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class StringFormatTransformer implements Transformer
{
    private string $entryName;

    private string $format;

    public function __construct(string $entryName, string $format)
    {
        $this->entryName = $entryName;
        $this->format = $format;
    }

    public function transform(Rows $rows) : Rows
    {
        /**
         * @psalm-var pure-callable(Row $row) : Row $transformer
         */
        $transformer = function (Row $row) : Row {
            $entry = $row->get($this->entryName);

            /** @psalm-suppress MixedArgument */
            return $row->remove(
                $entry->name()
            )->add(
                new Row\Entry\StringEntry($entry->name(), \sprintf($this->format, $entry->value()))
            );
        };

        return $rows->map($transformer);
    }
}
