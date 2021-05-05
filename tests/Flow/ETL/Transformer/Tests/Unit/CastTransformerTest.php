<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Tests\Unit;

use Flow\ETL\Row;
use Flow\ETL\Row\Entry\ArrayEntry;
use Flow\ETL\Row\Entry\DateEntry;
use Flow\ETL\Row\Entry\DateTimeEntry;
use Flow\ETL\Row\Entry\IntegerEntry;
use Flow\ETL\Row\Entry\JsonEntry;
use Flow\ETL\Row\Entry\StringEntry;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\Cast\CastJsonToArray;
use Flow\ETL\Transformer\Cast\CastToArray;
use Flow\ETL\Transformer\Cast\CastToDate;
use Flow\ETL\Transformer\Cast\CastToDateTime;
use Flow\ETL\Transformer\Cast\CastToInteger;
use Flow\ETL\Transformer\Cast\CastToJson;
use Flow\ETL\Transformer\Cast\CastToString;
use Flow\ETL\Transformer\CastTransformer;
use PHPUnit\Framework\TestCase;

final class CastTransformerTest extends TestCase
{
    public function test_string_to_datetime_transformer() : void
    {
        $entry = new StringEntry('date', '2020-01-01 00:00:00 UTC');

        $transformer = new CastTransformer(new CastToDateTime('date', 'Y-m-d H:i:s.P'));

        $rows = $transformer->transform(new Rows(new Row(new Row\Entries($entry))));

        $this->assertInstanceOf(DateTimeEntry::class, $rows->first()->get('date'));
        $this->assertSame('2020-01-01 00:00:00.+00:00', $rows->first()->valueOf('date'));
    }

    public function test_string_to_date_transformer() : void
    {
        $entry = new StringEntry('date', '2020-01-01 00:00:00 UTC');

        $transformer = new CastTransformer(new CastToDate('date'));

        $rows = $transformer->transform(new Rows(new Row(new Row\Entries($entry))));

        $this->assertInstanceOf(DateEntry::class, $rows->first()->get('date'));
        $this->assertSame('2020-01-01', $rows->first()->valueOf('date'));
    }

    public function test_string_to_integer() : void
    {
        $entry = new StringEntry('id', '123456');

        $transformer = new CastTransformer(new CastToInteger('id'));

        $rows = $transformer->transform(new Rows(new Row(new Row\Entries($entry))));

        $this->assertInstanceOf(Row\Entry\IntegerEntry::class, $rows->first()->get('id'));
        $this->assertSame(123456, $rows->first()->valueOf('id'));
    }

    public function test_cast_array_to_json() : void
    {
        $entry = new ArrayEntry('collection', ['foo' => 'bar']);

        $transformer = new CastTransformer(new CastToJson('collection'));

        $rows = $transformer->transform(new Rows(new Row(new Row\Entries($entry))));

        $this->assertInstanceOf(Row\Entry\JsonEntry::class, $rows->first()->get('collection'));
        $this->assertSame('{"foo":"bar"}', $rows->first()->valueOf('collection'));
    }

    public function test_integer_to_string() : void
    {
        $entry = new IntegerEntry('id', 123456);

        $transformer = new CastTransformer(new CastToString('id'));

        $rows = $transformer->transform(new Rows(new Row(new Row\Entries($entry))));

        $this->assertInstanceOf(Row\Entry\StringEntry::class, $rows->first()->get('id'));
        $this->assertSame('123456', $rows->first()->valueOf('id'));
    }

    public function test_integer_to_array() : void
    {
        $entry = new IntegerEntry('ids', 123456);

        $transformer = new CastTransformer(new CastToArray('ids'));

        $rows = $transformer->transform(new Rows(new Row(new Row\Entries($entry))));

        $this->assertInstanceOf(Row\Entry\ArrayEntry::class, $rows->first()->get('ids'));
        $this->assertSame([123456], $rows->first()->valueOf('ids'));
    }

    public function test_json_to_array() : void
    {
        $entry = new JsonEntry('ids', [123456]);

        $transformer = new CastTransformer(new CastJsonToArray('ids'));

        $rows = $transformer->transform(new Rows(new Row(new Row\Entries($entry))));

        $this->assertInstanceOf(Row\Entry\ArrayEntry::class, $rows->first()->get('ids'));
        $this->assertSame([123456], $rows->first()->valueOf('ids'));
    }

    public function test_string_json_to_array() : void
    {
        $entry = new StringEntry('ids', '[123456]');

        $transformer = new CastTransformer(new CastJsonToArray('ids'));

        $rows = $transformer->transform(new Rows(new Row(new Row\Entries($entry))));

        $this->assertInstanceOf(Row\Entry\ArrayEntry::class, $rows->first()->get('ids'));
        $this->assertSame([123456], $rows->first()->valueOf('ids'));
    }
}