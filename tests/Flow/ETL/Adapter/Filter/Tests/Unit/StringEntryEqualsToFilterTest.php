<?php

declare(strict_types=1);

namespace Flow\ETL\Adapter\Filter\Tests\Unit;

use Flow\ETL\Adapter\Filter\StringEntryEqualsTo;
use Flow\ETL\Row;
use PHPUnit\Framework\TestCase;

final class StringEntryEqualsToFilterTest extends TestCase
{
    public function test_that_string_entry_is_equals_to() : void
    {
        $filter = new StringEntryEqualsTo('test-entry', 'test-value');

        $this->assertTrue($filter(Row::create(Row\Entry\StringEntry::lowercase('test-entry', 'test-value'))));
    }

    public function test_that_string_entry_is_not_equals_to() : void
    {
        $filter = new StringEntryEqualsTo('test-entry', 'test-value');

        $this->assertFalse($filter(Row::create(Row\Entry\StringEntry::lowercase('test-entry', 'test-value-random'))));
    }

    public function test_that_entry_is_not_a_string() : void
    {
        $filter = new StringEntryEqualsTo('test-entry', 'test-value');

        $this->assertFalse($filter(Row::create(new Row\Entry\JsonEntry('test-entry', []))));
    }
}
