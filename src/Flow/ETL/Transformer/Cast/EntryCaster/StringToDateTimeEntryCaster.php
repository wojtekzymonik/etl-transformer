<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast\EntryCaster;

use Flow\ETL\Row\Entry;
use Flow\ETL\Row\Entry\DateTimeEntry;
use Flow\ETL\Transformer\Cast\EntryCaster;
use Flow\ETL\Transformer\Cast\ValueCaster;

/**
 * @psalm-immutable
 */
final class StringToDateTimeEntryCaster implements EntryCaster
{
    private string $format;

    private ValueCaster $valueCaster;

    /**
     * $timezone - this value should be used for datetime values that does not come with explicit tz to avoid using system default.
     * For example when the datetime is "2020-01-01 00:00:00" and we know that it's utc, then $timeZone should be set to 'UTC'.
     *
     * $toTimeZone - this value should be used to convert datetime to different timezone. So when the datetime comes in one timezone
     * "2020-01-01 00:00:00 UTC" and we want to convert it to America/Los_Angeles use $toTimeZone = 'America/Los_Angeles".
     * If datetime comes without origin timezone, like for example '2020-01-01 00:00:00' but we know it's UTC
     * and we want to cast it to 'America/Los_Angeles' use $timeZone = 'UTC' and $toTimeZone = 'America/Los_Angeles'.
     *
     * @param string $format
     * @param null|string $timeZone
     * @param null|string $toTimeZone
     */
    public function __construct(string $format, ?string $timeZone = null, ?string $toTimeZone = null)
    {
        $this->valueCaster = new ValueCaster\StringToDateTimeCaster($timeZone, $toTimeZone);
        $this->format = $format;
    }

    public function cast(Entry $entry) : Entry
    {
        /** @psalm-suppress MixedArgument */
        return new DateTimeEntry(
            $entry->name(),
            $this->valueCaster->cast($entry->value()),
            $this->format
        );
    }
}
