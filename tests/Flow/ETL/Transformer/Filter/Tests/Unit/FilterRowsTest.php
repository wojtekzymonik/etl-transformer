<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Filter\Tests\Unit;

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\Filter\FilterRows;
use Flow\ETL\Transformer\Filter\StringEntryEqualsTo;
use PHPUnit\Framework\TestCase;

final class FilterRowsTest extends TestCase
{
    public function test_filter_rows() : void
    {
        $filterRows = new FilterRows(
            new StringEntryEqualsTo('status', 'NEW'),
        );

        $rows = $filterRows->transform(
            new Rows(
                Row::create(new Row\Entry\StringEntry('status', 'PENDING')),
                Row::create(new Row\Entry\StringEntry('status', 'SHIPPED')),
                Row::create(new Row\Entry\StringEntry('status', 'NEW')),
            )
        );

        $this->assertEquals(
            [
                ['status' => 'NEW'],
            ],
            $rows->toArray()
        );
    }
}
