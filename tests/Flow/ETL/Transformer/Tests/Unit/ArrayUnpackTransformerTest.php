<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Tests\Unit;

use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\ArrayUnpackTransformer;
use Flow\ETL\Transformer\RemoveEntriesTransformer;
use PHPUnit\Framework\TestCase;

final class ArrayUnpackTransformerTest extends TestCase
{
    public function test_array_unpack_for_not_array_entry() : void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"integer_entry" is not ArrayEntry');

        $arrayUnpackTransformer = new ArrayUnpackTransformer('integer_entry');

        (new RemoveEntriesTransformer('integer_entry'))->transform(
            $arrayUnpackTransformer->transform(
                new Rows(
                    Row::create(
                        new Row\Entry\IntegerEntry('integer_entry', 1),
                    ),
                ),
            )
        );
    }

    public function test_array_unpack_for_not_existing_entry() : void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"array_entry" not found');

        $arrayUnpackTransformer = new ArrayUnpackTransformer('array_entry');

        (new RemoveEntriesTransformer('integer_entry'))->transform(
            $arrayUnpackTransformer->transform(
                new Rows(
                    Row::create(
                        new Row\Entry\IntegerEntry('integer_entry', 1),
                    ),
                ),
            )
        );
    }

    public function test_array_unpack_transformer() : void
    {
        $arrayUnpackTransformer = new ArrayUnpackTransformer('array_entry');

        $rows = (new RemoveEntriesTransformer('array_entry'))->transform(
            $arrayUnpackTransformer->transform(
                new Rows(
                    Row::create(
                        new Row\Entry\IntegerEntry('old_int', 1000),
                        new Row\Entry\ArrayEntry('array_entry', [
                            'id' => 1,
                            'status' => 'PENDING',
                            'enabled' => true,
                            'datetime' =>  new \DateTimeImmutable('2020-01-01 00:00:00 UTC'),
                            'array' => ['foo', 'bar'],
                            'json' => '["foo", "bar"]',
                            'object' => new \stdClass(),
                            'null' => null,
                        ]),
                    ),
                ),
            )
        );

        $this->assertEquals(
            new Rows(
                Row::create(
                    new Row\Entry\IntegerEntry('old_int', 1000),
                    new Row\Entry\IntegerEntry('id', 1),
                    new Row\Entry\StringEntry('status', 'PENDING'),
                    new Row\Entry\BooleanEntry('enabled', true),
                    new Row\Entry\DateTimeEntry('datetime', new \DateTimeImmutable('2020-01-01 00:00:00 UTC')),
                    new Row\Entry\ArrayEntry('array', ['foo', 'bar']),
                    new Row\Entry\JsonEntry('json', ['foo', 'bar']),
                    new Row\Entry\ObjectEntry('object', new \stdClass()),
                    new Row\Entry\NullEntry('null')
                ),
            ),
            $rows
        );
    }

    public function test_array_unpack_transformer_for_non_associative_array() : void
    {
        $arrayUnpackTransformer = new ArrayUnpackTransformer('array_entry');

        $rows = (new RemoveEntriesTransformer('array_entry'))->transform(
            $arrayUnpackTransformer->transform(
                new Rows(
                    Row::create(
                        new Row\Entry\ArrayEntry('array_entry', [
                            1,
                            'PENDING',
                            true,
                            new \DateTimeImmutable('2020-01-01 00:00:00 UTC'),
                            ['foo', 'bar'],
                            '["foo", "bar"]',
                            new \stdClass(),
                            null,
                            0.25,
                        ]),
                    ),
                ),
            )
        );

        $this->assertEquals(
            new Rows(
                Row::create(
                    new Row\Entry\IntegerEntry('0', 1),
                    new Row\Entry\StringEntry('1', 'PENDING'),
                    new Row\Entry\BooleanEntry('2', true),
                    new Row\Entry\DateTimeEntry('3', new \DateTimeImmutable('2020-01-01 00:00:00 UTC')),
                    new Row\Entry\ArrayEntry('4', ['foo', 'bar']),
                    new Row\Entry\JsonEntry('5', ['foo', 'bar']),
                    new Row\Entry\ObjectEntry('6', new \stdClass()),
                    new Row\Entry\NullEntry('7'),
                    new Row\Entry\StringEntry('8', '0.25'),
                ),
            ),
            $rows
        );
    }

    public function test_array_unpack_with_integer() : void
    {
        $arrayUnpackTransformer = new ArrayUnpackTransformer('array_entry');

        $rows = (new RemoveEntriesTransformer('array_entry'))->transform(
            $arrayUnpackTransformer->transform(
                new Rows(
                    Row::create(new Row\Entry\ArrayEntry('array_entry', ['id' => '1']), ),
                ),
            )
        );

        $this->assertEquals(
            new Rows(
                Row::create(new Row\Entry\StringEntry('id', '1')),
            ),
            $rows
        );
    }
}
