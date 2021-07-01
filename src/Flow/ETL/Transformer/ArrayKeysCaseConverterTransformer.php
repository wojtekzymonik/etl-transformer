<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer;

use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer;
use Flow\ETL\Transformer\CaseConverter\ArrayKeyConverter;
use Flow\ETL\Transformer\CaseConverter\CaseStyles;
use Flow\ETL\Transformer\Factory\NativeEntryFactory;
use Jawira\CaseConverter\Convert;

/**
 * @psalm-immutable
 */
final class ArrayKeysCaseConverterTransformer implements Transformer
{
    private string $arrayEntryName;

    private string $style;

    private EntryFactory $entryFactory;

    public function __construct(
        string $arrayEntryName,
        string $style,
        EntryFactory $entryFactory = null
    ) {
        /** @psalm-suppress ImpureFunctionCall */
        if (!\class_exists('Jawira\CaseConverter\Convert')) {
            throw new RuntimeException("Jawira\CaseConverter\Convert class not found, please add jawira/case-converter dependency to the project first.");
        }

        if (!\in_array($style, CaseStyles::ALL, true)) {
            throw new InvalidArgumentException("Unrecognized style {$style}, please use one of following: " . \implode(', ', CaseStyles::ALL));
        }

        $this->arrayEntryName = $arrayEntryName;
        $this->style = $style;
        $this->entryFactory = $entryFactory ?? new NativeEntryFactory();
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

            return $row->set(
                $this->entryFactory->createEntry(
                    $arrayEntry->name(),
                    (new ArrayKeyConverter(
                        /** @phpstan-ignore-next-line */
                        fn (string $key) : string => (string) \call_user_func([new Convert($key), 'to' . \ucfirst($this->style)])
                    ))->convert($arrayEntry->value())
                )
            );
        };

        return $rows->map($transformer);
    }
}
