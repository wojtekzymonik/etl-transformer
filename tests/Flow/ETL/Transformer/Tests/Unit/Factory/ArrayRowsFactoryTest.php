<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Tests\Unit\Factory;

use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Transformer\Factory\ArrayRowsFactory;
use PHPUnit\Framework\TestCase;

final class ArrayRowsFactoryTest extends TestCase
{
    public function test_creating_rows_from_flat_array() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ArrayRowsFactory expects data to be an array of arrays');

        new ArrayRowsFactory([1, 2, 3]);
    }

    public function test_create_rows_from_array() : void
    {
        $factory = new ArrayRowsFactory($data = [['id' => 1], ['id' => 2], ['id' => 3]]);

        $rows = $factory->create();

        $this->assertSame($data, $rows->toArray());
    }
}
