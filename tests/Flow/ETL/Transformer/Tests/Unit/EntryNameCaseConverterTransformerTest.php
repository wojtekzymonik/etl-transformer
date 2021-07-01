<?php

declare(strict_types=1);

namespace Flow\ETL\Transformer\Tests\Unit;

use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\CaseConverter\CaseStyles;
use Flow\ETL\Transformer\EntryNameCaseConverterTransformer;
use PHPUnit\Framework\TestCase;

final class EntryNameCaseConverterTransformerTest extends TestCase
{
    public function test_using_invalid_style() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unrecognized style wrong style, please use one of following: camel, pascal, snake, ada, macro, kebab, train, cobol, lower, upper, title, sentence');

        new EntryNameCaseConverterTransformer('wrong style');
    }

    public function test_conversion_of_entry_names_case() : void
    {
        $transformer = new EntryNameCaseConverterTransformer(CaseStyles::SNAKE);

        $rows = $transformer->transform(new Rows(
            Row::create(
                new Row\Entry\StringEntry('CamelCaseEntryName', 'test'),
                new Row\Entry\StringEntry('otherCaseEntryName', 'test'),
            )
        ));

        $this->assertSame(
            [
                [
                    'camel_case_entry_name' => 'test',
                    'other_case_entry_name' => 'test',
                ],
            ],
            $rows->toArray()
        );
    }
}
