<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Tests\Unit\Factory;

use Flow\ETL\Row\Entry\DateTimeEntry;
use Flow\ETL\Row\Entry\NullEntry;
use Flow\ETL\Transformer\Cast\CastToDateTime;
use Flow\ETL\Transformer\Factory\ArrayRowsFactory;
use Flow\ETL\Transformer\Factory\CastedRowsFactory;
use PHPUnit\Framework\TestCase;

final class CasedRowsFactoryTest extends TestCase
{
    public function test_creating_casted_rows_nullable() : void
    {
        $data = [
            ['id' => 1, 'name' => 'Norbert', 'roles' => ['USER', 'ADMIN'], 'blocked_at' => null],
        ];

        $rows = (new CastedRowsFactory(
            new ArrayRowsFactory($data),
            CastToDateTime::nullable(['blocked_at'], 'Y-m-d H:i:s', 'UTC')
        ))->create();

        $this->assertInstanceOf(NullEntry::class, $rows->first()->get('blocked_at'));
    }

    public function test_creating_casted_rows_not_nullable() : void
    {
        $data = [
            ['id' => 2, 'name' => 'John', 'roles' => ['USER'], 'blocked_at' => new \DateTimeImmutable('2020-01-01 00:00:00')],
        ];

        $rows = (new CastedRowsFactory(
            new ArrayRowsFactory($data),
            new CastToDateTime(['blocked_at'], 'Y-m-d H:i:s', 'UTC')
        ))->create();

        $this->assertInstanceOf(DateTimeEntry::class, $rows->first()->get('blocked_at'));
    }
}
