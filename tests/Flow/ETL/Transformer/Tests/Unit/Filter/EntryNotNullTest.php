<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Tests\Unit\Filter;

use Flow\ETL\Row;
use Flow\ETL\Transformer\Filter\Filter\EntryNotNull;
use PHPUnit\Framework\TestCase;

final class EntryNotNullTest extends TestCase
{
    public function test_that_string_entry_is_not_null() : void
    {
        $filter = new EntryNotNull('test-entry');

        $this->assertTrue($filter(Row::create(Row\Entry\StringEntry::lowercase('test-entry', 'test-value'))));
    }

    public function test_that_null_entry_is_null() : void
    {
        $filter = new EntryNotNull('test-entry');

        $this->assertFalse($filter(Row::create(new Row\Entry\NullEntry('test-entry'))));
    }
}
