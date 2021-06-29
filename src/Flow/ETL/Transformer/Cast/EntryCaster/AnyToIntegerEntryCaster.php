<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast\EntryCaster;

use Flow\ETL\Row\Entry;
use Flow\ETL\Row\Entry\IntegerEntry;
use Flow\ETL\Transformer\Cast\EntryCaster;
use Flow\ETL\Transformer\Cast\ValueCaster;

/**
 * @psalm-immutable
 */
final class AnyToIntegerEntryCaster implements EntryCaster
{
    private ValueCaster $valueCaster;

    public function __construct()
    {
        $this->valueCaster = new ValueCaster\AnyToIntegerCaster();
    }

    public function cast(Entry $entry) : Entry
    {
        /** @psalm-suppress MixedArgument */
        return new IntegerEntry(
            $entry->name(),
            $this->valueCaster->cast($entry->value())
        );
    }
}
