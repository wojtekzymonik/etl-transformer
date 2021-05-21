<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Cast;

use Flow\ETL\Row\Entry\DateTimeEntry;

final class CastToDateTime extends CastEntry
{
    /**
     * $timezone - this value should be used for datetime values that does not come with explicit tz to avoid using system default.
     * For example when the datetime is "2020-01-01 00:00:00" and we know that it's utc, then $timeZone should be set to 'UTC'.
     *
     * $toTimeZone - this value should be used to convert datetime to different timezone. So when the datetime comes in one timezone
     * "2020-01-01 00:00:00 UTC" and we want to convert it to America/Los_Angeles use $toTimeZone = 'America/Los_Angeles".
     * If datetime comes without origin timezone, like for example '2020-01-01 00:00:00' but we know it's UTC
     * and we want to cast it to 'America/Los_Angeles' use $timeZone = 'UTC' and $toTimeZone = 'America/Los_Angeles'.
     *
     * @param array<string> $entryNames
     * @param string $format
     * @param null|string $timeZone
     * @param null|string $toTimeZone
     *
     * @throws \Flow\ETL\Exception\InvalidArgumentException
     */
    public function __construct(array $entryNames, string $format, ?string $timeZone = null, ?string $toTimeZone = null)
    {
        parent::__construct(
            $entryNames,
            DateTimeEntry::class,
            [$format],
            function (string $dateTimeString) use ($timeZone, $toTimeZone) : \DateTimeImmutable {
                if ($timeZone && $toTimeZone) {
                    return (new \DateTimeImmutable($dateTimeString, new \DateTimeZone($timeZone)))->setTimezone(new \DateTimeZone($toTimeZone));
                }

                if ($timeZone) {
                    return new \DateTimeImmutable($dateTimeString, new \DateTimeZone($timeZone));
                }

                if ($toTimeZone) {
                    return (new \DateTimeImmutable($dateTimeString))->setTimezone(new \DateTimeZone($toTimeZone));
                }

                return new \DateTimeImmutable($dateTimeString);
            }
        );
    }
}
