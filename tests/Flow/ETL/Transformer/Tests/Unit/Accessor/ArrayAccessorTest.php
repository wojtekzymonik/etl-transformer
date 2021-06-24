<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Tests\Unit\Accessor;

use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Transformer\Accessor\ArrayAccessor;
use PHPUnit\Framework\TestCase;

final class ArrayAccessorTest extends TestCase
{
    public function test_accessing_array_value_by_path() : void
    {
        $this->assertSame(ArrayAccessor::value(['user' => ['id' => 1]], 'user.id'), 1);
        $this->assertTrue(ArrayAccessor::pathExists(['user' => ['id' => 1]], 'user.id'));
        $this->assertFalse(ArrayAccessor::pathExists(['user' => ['id' => 1]], 'invalid_path'));
    }

    public function test_accessing_next_array_value_by_numeric_path() : void
    {
        $this->assertSame(ArrayAccessor::value(['users' => [['user' => ['id' => 1]]]], 'users.0.user.id'), 1);
    }

    public function test_accessing_array_value_by_invalid_path() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Path \"invalid_path\" does not exists in array \"array('user'=>array('id'=>1,),)\"");

        $this->assertSame(ArrayAccessor::value(['user' => ['id' => 1]], 'invalid_path'), 1);
    }

    public function test_accessing_empty_array_value_by_invalid_path() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Path "invalid_path" does not exists in array "array()"');

        $this->assertSame(ArrayAccessor::value([], 'invalid_path'), 1);
    }
}
