<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Tests\Unit\Cast;

use Flow\ETL\Row;
use Flow\ETL\Row\Entry\StringEntry;
use Flow\ETL\Transformer\Cast\CastToDate;
use PHPUnit\Framework\TestCase;

final class CastToDateTest extends TestCase
{
    public function test_cast_string_to_date() : void
    {
        $this->assertEquals(
            [
                'date' => '2020-01-01',
            ],
            (new CastToDate(['date'], false))->cast(Row::create(new StringEntry('date', '2020-01-01')))->toArray()
        );
    }
}
