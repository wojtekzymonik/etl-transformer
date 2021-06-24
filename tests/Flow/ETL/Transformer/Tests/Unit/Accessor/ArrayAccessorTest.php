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

    public function test_accessing_array_value_by_nullsafe_path() : void
    {
        $this->assertNull(ArrayAccessor::value(['user' => ['id' => 1]], 'user.?name'));
        $this->assertNull(ArrayAccessor::value(['user' => ['role' => ['name' => 'admin']]], 'user.?wrong_path.name'));
        $this->assertNull(ArrayAccessor::value(['users' => []], 'users.?0.name'));
    }

    public function test_accessing_nested_array_value_by_numeric_path() : void
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

    public function test_accessing_array_scalar_value_by_path_with_asterix() : void
    {
        $this->assertSame(
            ['Michael', 'Jack'],
            ArrayAccessor::value(
                ['users' => [['user' => ['id' => 1, 'name' => 'Michael']], ['user' => ['id' => 2, 'name' => 'Jack']]]],
                'users.*.user.name'
            ),
        );
    }

    public function test_accessing_array_value_by_path_with_asterix() : void
    {
        $this->assertSame(
            [['id' => 1, 'name' => 'Michael'], ['id' => 2, 'name' => 'Jack']],
            ArrayAccessor::value(
                ['users' => [['user' => ['id' => 1, 'name' => 'Michael']], ['user' => ['id' => 2, 'name' => 'Jack']]]],
                'users.*.user'
            ),
        );
    }

    public function test_accessing_array_scalar_value_by_path_with_asterix_and_different_elements() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Path \"user.name\" does not exists in array \"array('user'=>array('id'=>2,),)\"");

        ArrayAccessor::value(
            ['users' => [['user' => ['id' => 1, 'name' => 'Michael']], ['user' => ['id' => 2]]]],
            'users.*.user.name'
        );
    }

    public function test_accessing_array_scalar_value_by_path_with_escaped_asterix_key() : void
    {
        $this->assertSame(
            'Michael',
            ArrayAccessor::value(
                ['users' => ['*' => ['id' => 1, 'name' => 'Michael']]],
                'users.\\*.name'
            ),
        );
    }

    public function test_accessing_array_scalar_value_by_path_with_nullable_asterix_and_different_elements() : void
    {
        $this->assertSame(
            ['Michael'],
            ArrayAccessor::value(
                ['users' => [['user' => ['id' => 1, 'name' => 'Michael']], ['user' => ['id' => 2]]]],
                'users.?*.user.name'
            ),
        );
    }

    public function test_accessing_array_scalar_value_by_path_with_asterix_and_different_elements_using_nullsafe() : void
    {
        $this->assertSame(
            ['Michael', null],
            ArrayAccessor::value(
                ['users' => [['user' => ['id' => 1, 'name' => 'Michael']], ['user' => ['id' => 2]]]],
                'users.*.user.?name'
            ),
        );
    }

    public function test_accessing_array_scalar_value_by_path_with_escaped_nullable_asterix() : void
    {
        $this->assertSame(
            'Michael',
            ArrayAccessor::value(
                ['users' => ['?*' => ['id' => 1, 'name' => 'Michael']]],
                'users.\\?*.name'
            )
        );
    }

    public function test_accessing_array_scalar_value_by_path_multiple_asterix_paths() : void
    {
        $this->assertSame(
            [
                ['12345', '22222'],
                ['3333'],
            ],
            ArrayAccessor::value(
                ['transactions' => [
                    ['id' => 1, 'packages' => [['label_id' => '12345'], ['label_id' => '22222']]],
                    ['id' => 1, 'packages' => [['label_id' => '3333']]],
                ],
                ],
                'transactions.*.packages.*.label_id'
            ),
        );
    }

    public function test_accessing_array_scalar_value_by_path_multiple_asterix_paths_with_nullsafe() : void
    {
        $this->assertSame(
            [
                ['12345', '22222'],
                ['3333'],
                [null],
            ],
            ArrayAccessor::value(
                ['transactions' => [
                    ['id' => 1, 'packages' => [['label_id' => '12345'], ['label_id' => '22222']]],
                    ['id' => 1, 'packages' => [['label_id' => '3333']]],
                    ['id' => 1, 'packages' => [['foo' => 'bar']]],
                ],
                ],
                'transactions.*.packages.*.?label_id'
            ),
        );
    }
}
