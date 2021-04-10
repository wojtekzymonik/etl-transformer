<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Tests\Unit\Filter;

use Flow\ETL\Row;
use Flow\ETL\Transformer\Filter\Filter\EntryEqualsTo;
use PHPUnit\Framework\TestCase;

final class EntryEqualsToTest extends TestCase
{
    public function test_that_string_entry_is_equals_to() : void
    {
        $filter = new EntryEqualsTo('test-entry', 'test-value');

        $this->assertTrue($filter(Row::create(Row\Entry\StringEntry::lowercase('test-entry', 'test-value'))));
    }

    public function test_that_string_entry_is_not_equals_to() : void
    {
        $filter = new EntryEqualsTo('test-entry', 'test-value');

        $this->assertFalse($filter(Row::create(Row\Entry\StringEntry::lowercase('test-entry', 'test-value-random'))));
    }

    public function test_that_entry_is_not_a_string() : void
    {
        $filter = new EntryEqualsTo('test-entry', 'test-value');

        $this->assertFalse($filter(Row::create(new Row\Entry\JsonEntry('test-entry', []))));
    }
}
