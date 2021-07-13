<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;

/**
 * @psalm-immutable
 */
final class StringEntryValueCaseConverterTransformer implements Transformer
{
    private const CASE_UPPER = 'upper';

    private const CASE_LOWER = 'lower';

    private string $case;

    /**
     * @var string[]
     */
    private array $entryNames;

    private function __construct(string $case, string ...$entryNames)
    {
        $this->case = $case;
        $this->entryNames = $entryNames;
    }

    public static function upper(string ...$entryNames) : self
    {
        return new self(self::CASE_UPPER, ...$entryNames);
    }

    public static function lower(string ...$entryNames) : self
    {
        return new self(self::CASE_LOWER, ...$entryNames);
    }

    public function transform(Rows $rows) : Rows
    {
        /**
         * @psalm-var pure-callable(Row $row) : Row $transformer
         */
        $transformer = function (Row $row) : Row {
            foreach ($this->entryNames as $entryName) {
                /** @var Row\Entry\StringEntry $entry */
                $entry = $row->get($entryName);

                $row = $row->remove(
                    $entry->name()
                )->add(
                    /** @psalm-suppress MixedArgument */
                    new Row\Entry\StringEntry($entry->name(), ($this->case === self::CASE_UPPER) ? \mb_strtoupper($entry->value()) : \mb_strtolower($entry->value()))
                );
            }

            return $row;
        };

        return $rows->map($transformer);
    }
}
