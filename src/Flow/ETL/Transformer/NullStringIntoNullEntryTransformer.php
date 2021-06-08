<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class NullStringIntoNullEntryTransformer implements Transformer
{
    /**
     * @var string[]
     */
    private array $entryNames;

    public function __construct(string ...$entryNames)
    {
        $this->entryNames = $entryNames;
    }

    public function transform(Rows $rows) : Rows
    {
        /**
         * @psalm-var pure-callable(Row $row) : Row $transformer
         */
        $transformer = function (Row $row) : Row {
            foreach ($this->entryNames as $entryName) {
                $entry = $row->get($entryName);

                if (!\is_string($entry->value())) {
                    continue;
                }

                if (\mb_strtolower($entry->value()) === 'null') {
                    $row = $row
                        ->remove($entry->name())
                        ->add(new Row\Entry\NullEntry($entry->name()));
                }
            }

            return $row;
        };

        return $rows->map($transformer);
    }
}
