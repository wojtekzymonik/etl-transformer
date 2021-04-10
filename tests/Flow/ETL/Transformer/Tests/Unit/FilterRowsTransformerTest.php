<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Tests\Unit;

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\Filter\Filter\EntryEqualsTo;
use Flow\ETL\Transformer\FilterRowsTransformer;
use PHPUnit\Framework\TestCase;

final class FilterRowsTransformerTest extends TestCase
{
    public function test_filter_string_rows() : void
    {
        $filterRows = new FilterRowsTransformer(
            new EntryEqualsTo('status', 'NEW'),
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

    public function test_filter_numeric_rows() : void
    {
        $filterRows = new FilterRowsTransformer(
            new EntryEqualsTo('number', 5),
        );

        $rows = $filterRows->transform(
            new Rows(
                Row::create(new Row\Entry\IntegerEntry('number', 2)),
                Row::create(new Row\Entry\IntegerEntry('number', 10)),
                Row::create(new Row\Entry\IntegerEntry('number', 5)),
            )
        );

        $this->assertEquals(
            [
                ['number' => 5],
            ],
            $rows->toArray()
        );
    }
}
