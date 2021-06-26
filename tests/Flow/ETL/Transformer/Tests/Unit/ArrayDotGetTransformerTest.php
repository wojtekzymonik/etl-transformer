<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Tests\Unit;

use Flow\ArrayDot\Exception\InvalidPathException;
use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\ArrayDotGetTransformer;
use PHPUnit\Framework\TestCase;

final class ArrayDotGetTransformerTest extends TestCase
{
    public function test_array_access_for_not_array_entry() : void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('integer_entry is not ArrayEntry but Flow\ETL\Row\Entry\IntegerEntry');

        $arrayUnpackTransformer = new ArrayDotGetTransformer('integer_entry', 'invalid_path');

        $arrayUnpackTransformer->transform(
            new Rows(
                Row::create(
                    new Row\Entry\IntegerEntry('integer_entry', 1),
                ),
            ),
        );
    }

    public function test_array_accessor_transformer() : void
    {
        $arrayAccessorTransformer = new ArrayDotGetTransformer('array_entry', 'array.foo');

        $rows = $arrayAccessorTransformer->transform(
            new Rows(
                Row::create(
                    new Row\Entry\ArrayEntry('array_entry', [
                        'id' => 1,
                        'status' => 'PENDING',
                        'enabled' => true,
                        'array' => ['foo' => 'bar'],
                    ]),
                ),
            ),
        );

        $this->assertEquals(
            new Row\Entry\StringEntry('array.foo', 'bar'),
            $rows->first()->get('array.foo')
        );
    }

    public function test_array_accessor_transformer_with_invalid_but_strict_path() : void
    {
        $arrayAccessorTransformer = new ArrayDotGetTransformer('array_entry', 'invalid_path', true);

        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessage('Path "invalid_path" does not exists in array ');

        $rows = $arrayAccessorTransformer->transform(
            new Rows(
                Row::create(
                    new Row\Entry\ArrayEntry('array_entry', [
                        'id' => 1,
                        'status' => 'PENDING',
                        'enabled' => true,
                        'datetime' =>  new \DateTimeImmutable('2020-01-01 00:00:00 UTC'),
                        'array' => ['foo' => 'bar'],
                    ]),
                ),
            ),
        );
    }

    public function test_array_accessor_transformer_with_invalid_and_without_strict_path() : void
    {
        $arrayAccessorTransformer = new ArrayDotGetTransformer('array_entry', 'invalid_path', false);

        $rows = $arrayAccessorTransformer->transform(
            new Rows(
                Row::create(
                    new Row\Entry\ArrayEntry('array_entry', [
                        'id' => 1,
                        'status' => 'PENDING',
                        'enabled' => true,
                        'datetime' =>  new \DateTimeImmutable('2020-01-01 00:00:00 UTC'),
                        'array' => ['foo' => 'bar'],
                    ]),
                ),
            ),
        );

        $this->assertEquals(
            new Row\Entry\NullEntry('invalid_path'),
            $rows->first()->get('invalid_path')
        );
    }
}
