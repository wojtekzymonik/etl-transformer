# ETL Transformers

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)

## Description

Set of ETL generic Transformers

## Transformer - FilterRows

Filter out rows

```php 
<?php

use Flow\ETL\Transformer\Filter\EntryEqualsTo;
use Flow\ETL\Transformer\FilterRowsTransformer;
use Flow\ETL\Row;
use Flow\ETL\Rows;

$transformer = new FilterRowsTransformer(
    new EntryEqualsTo('status', 'NEW'),
);

$transformer->transform(
    new Rows(
        Row::create(new Row\Entry\StringEntry('status', 'PENDING')),
        Row::create(new Row\Entry\StringEntry('status', 'SHIPPED')),
        Row::create(new Row\Entry\StringEntry('status', 'NEW')),
    )
);

```

Available Filters

- [All](src/Flow/ETL/Transformer/Filter/Filter/All.php)
- [Any](src/Flow/ETL/Transformer/Filter/Filter/Any.php)
- [Callback](src/Flow/ETL/Transformer/Filter/Filter/Callback.php)
- [EntryEqualsTo](src/Flow/ETL/Transformer/Filter/Filter/EntryEqualsTo.php)
- [EntryNotEqualsTo](src/Flow/ETL/Transformer/Filter/Filter/EntryNotEqualsTo.php)
- [EntryNotNull](src/Flow/ETL/Transformer/Filter/Filter/EntryNotNull.php)
- [EntryNotNumber](src/Flow/ETL/Transformer/Filter/Filter/EntryNotNumber.php)
- [EntryNumber](src/Flow/ETL/Transformer/Filter/Filter/EntryNumber.php)
- [EntryExists](src/Flow/ETL/Transformer/Filter/Filter/EntryExists.php)
- [Opposite](src/Flow/ETL/Transformer/Filter/Filter/Opposite.php)
- [ValidValue](src/Flow/ETL/Transformer/Filter/Filter/ValidValue.php) - optionally integrates with [Symfony Validator](https://github.com/symfony/validator)

## Transformer - Conditional

Transforms only those Rows that met given condition. 

```php 

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\ChainTransformer;
use Flow\ETL\Transformer\Condition\All;
use Flow\ETL\Transformer\Condition\EntryValueEqualsTo;
use Flow\ETL\Transformer\ConditionalTransformer;
use Flow\ETL\Transformer\NewStaticEntryTransformer;

$transformer = new ChainTransformer(
    new ConditionalTransformer(
        new All(
            new EntryValueEqualsTo('first_name', 'Michael'),
            new EntryValueEqualsTo('last_name', 'Jackson'),
        ),
        new NewStaticEntryTransformer(new Row\Entry\StringEntry('profession', 'singer'))
    ),
    new ConditionalTransformer(
        new All(
            new EntryValueEqualsTo('first_name', 'Rocky'),
            new EntryValueEqualsTo('last_name', 'Balboa'),
        ),
        new NewStaticEntryTransformer(new Row\Entry\StringEntry('profession', 'boxer'))
    )
);

$rows = new Rows(
    Row::create(
        new Row\Entry\IntegerEntry('id', 1),
        new Row\Entry\StringEntry('first_name', 'Michael'),
        new Row\Entry\StringEntry('last_name', 'Jackson'),
    ),
    Row::create(
        new Row\Entry\IntegerEntry('id', 2),
        new Row\Entry\StringEntry('first_name', 'Rocky'),
        new Row\Entry\StringEntry('last_name', 'Balboa'),
    )
);

$this->assertSame(
    [
        [
            'id' => 1,
            'first_name' => 'Michael',
            'last_name' => 'Jackson',
            'profession' => 'singer',
        ],
        [
            'id' => 2,
            'first_name' => 'Rocky',
            'last_name' => 'Balboa',
            'profession' => 'boxer',
        ]
    ],
    $transformer->transform($rows)->toArray()
);
```

Available Conditions 

- [All](src/Flow/ETL/Transformer/Condition/All.php)
- [Any](src/Flow/ETL/Transformer/Condition/Any.php)
- [ArrayDotExists](src/Flow/ETL/Transformer/Condition/ArrayDotExists.php)
- [ArrayDotValueEqualsTo](src/Flow/ETL/Transformer/Condition/ArrayDotValueEqualsTo.php)
- [ArrayDotValueGreaterOrEqualThan](src/Flow/ETL/Transformer/Condition/ArrayDotValueGreaterOrEqualThan.php)
- [ArrayDotValueGreaterThan](src/Flow/ETL/Transformer/Condition/ArrayDotValueGreaterThan.php)
- [ArrayDotValueLessOrEqualThan](src/Flow/ETL/Transformer/Condition/ArrayDotValueLessOrEqualThan.php)
- [ArrayDotValueLessThan](src/Flow/ETL/Transformer/Condition/ArrayDotValueLessThan.php)
- [EntryExists](src/Flow/ETL/Transformer/Condition/EntryExists.php)
- [EntryInstanceOf](src/Flow/ETL/Transformer/Condition/EntryInstanceOf.php)
- [EntryNotNull](src/Flow/ETL/Transformer/Condition/EntryNotNull.php)
- [EntryValueEqualsTo](src/Flow/ETL/Transformer/Condition/EntryValueEqualsTo.php)
- [EntryValueGreaterOrEqualThan](src/Flow/ETL/Transformer/Condition/EntryValueGreaterOrEqualThan.php)
- [EntryValueGreaterThan](src/Flow/ETL/Transformer/Condition/EntryValueGreaterThan.php)
- [EntryValueLessOrEqualThan](src/Flow/ETL/Transformer/Condition/EntryValueLessOrEqualThan.php)
- [EntryValueLessThan](src/Flow/ETL/Transformer/Condition/EntryValueLessThan.php)
- [None](src/Flow/ETL/Transformer/Condition/None.php)
- [Opposite](src/Flow/ETL/Transformer/Condition/Opposite.php)
- [ValidValue](src/Flow/ETL/Transformer/Condition/ValidValue) - optionally integrates with [Symfony Validator](https://github.com/symfony/validator)

## Transformer - RemoveEntriesTransformer

Remove transformers by name from each row.

```php
<?php

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\RemoveEntriesTransformer;

$transformer = new RemoveEntriesTransformer('id', 'array');

$transformer->transform(
    Row::create(
        new Row\Entry\IntegerEntry('id', 1),
        new Row\Entry\StringEntry('name', 'Row Name'),
        new Row\Entry\ArrayEntry('array', ['test'])
    )
);
```

## Transformer - ArraySort

```php

use Flow\ETL\Row;
use Flow\ETL\Row\Entry\ArrayEntry;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\ArraySortTransformer;

$arrayEntry = new ArrayEntry(
    'array',
    [
        5,
        3,
        10,
        4
    ]
);

$transformer = new ArraySortTransformer('array', \SORT_REGULAR);

$this->assertSame(
    [
        [
            'array' => [3, 4, 5, 10]
        ]
    ],
    $transformer->transform(new Rows(Row::create($arrayEntry)))->toArray()
);
```

## Transformer - ArrayReverse

```php
use Flow\ETL\Row;
use Flow\ETL\Row\Entry\ArrayEntry;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\ArrayReverseTransformer;

$arrayEntry = new ArrayEntry(
    'array',
    [
        5,
        3,
        10,
        4,
    ]
);

$transformer = new ArrayReverseTransformer('array', \SORT_REGULAR);

$this->assertSame(
    [
        [
            'array' => [4, 10, 3, 5],
        ],
    ],
    $transformer->transform(new Rows(Row::create($arrayEntry)))->toArray()
);
```

## Transformer - ArrayMerge

```php
use Flow\ETL\Row;
use Flow\ETL\Row\Entry\ArrayEntry;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\ArrayMergeTransformer;

$arrayOneEntry = new ArrayEntry(
    'array_one',
    [
        5,
        3,
        10,
        4,
    ]
);
$arrayTwoEntry = new ArrayEntry(
    'array_two',
    [
        'A',
        'Z',
        'C',
        'O',
    ]
);

$transformer = new ArrayMergeTransformer(['array_one', 'array_two']);

$this->assertSame(
    [
        [
            'array_one' => [5, 3, 10, 4],
            'array_two' => ['A', 'Z', 'C', 'O'],
            'merged' => [5, 3, 10, 4, 'A', 'Z', 'C', 'O'],
        ],
    ],
    $transformer->transform(new Rows(Row::create($arrayOneEntry, $arrayTwoEntry)))->toArray()
);
```

## Transformer - ArrayUnpack

Unpacks ArrayEntry into dedicated Entries detecting each array element type.

```php
<?php

use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\ArrayUnpackTransformer;
use Flow\ETL\Transformer\RemoveEntriesTransformer;

$arrayUnpackTransformer = new ArrayUnpackTransformer('array_entry');

$rows = (new RemoveEntriesTransformer('array_entry'))->transform(
    $arrayUnpackTransformer->transform(
        new Rows(
            Row::create(
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
```

## Transformer - Keep Entries

```php
<?php

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\KeepEntriesTransformer;

$rows = new Rows(
    Row::create(
        new Row\Entry\IntegerEntry('id', 1),
        new Row\Entry\StringEntry('name', 'Row Name'),
        new Row\Entry\ArrayEntry('array', ['test'])
    )
);

$transformer = new KeepEntriesTransformer('name');

$this->assertSame(
    [
        ['name' => 'Row Name'],
    ],
    $transformer->transform($rows)->toArray()
);
```

## Transformer - ObjectToArray

This transformer requires `laminas/laminas-hydrator` or `ocramius/generated-hydrator` in the project

```
composer require laminas/laminas-hydrator
```

```php
<?php

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\ObjectToArrayTransformer;
use Flow\ETL\Transformer\Tests\Fixtures\Example;
use GeneratedHydrator\Configuration;

$objectToArrayTransformer = new ObjectToArrayTransformer(
    (new Configuration(Example::class))->createFactory()->getHydrator(),
    'object_entry'
);

$rows = $objectToArrayTransformer->transform(
    new Rows(
        Row::create(
            new Row\Entry\IntegerEntry('old_int', 1000),
            new Row\Entry\ObjectEntry('object_entry', new Example()),
        ),
    ),
);

$this->assertEquals(
    new Rows(
        Row::create(
            new Row\Entry\IntegerEntry('old_int', 1000),
            new Row\Entry\ArrayEntry('object_entry', [
                'foo' => 1,
                'bar' => 2,
                'baz' => 3,
                'bad' => new \DateTimeImmutable('2020-01-01 00:00:00 UTC'),
            ])
        ),
    ),
    $rows
);

```

## Transformer - RenameEntries 


```php 
<?php

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\RenameEntries\EntryRename;
use Flow\ETL\Transformer\RenameEntriesTransformer;

$renameTransformer = new RenameEntriesTransformer(
    new EntryRename('old_int', 'new_int'),
    new EntryRename('null', 'nothing')
);

$rows = $renameTransformer->transform(
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
);

$this->assertEquals(
    new Rows(
        Row::create(
            new Row\Entry\IntegerEntry('id', 1),
            new Row\Entry\StringEntry('status', 'PENDING'),
            new Row\Entry\BooleanEntry('enabled', true),
            new Row\Entry\DateTimeEntry('datetime', new \DateTimeImmutable('2020-01-01 00:00:00 UTC')),
            new Row\Entry\ArrayEntry('array', ['foo', 'bar']),
            new Row\Entry\JsonEntry('json', ['foo', 'bar']),
            new Row\Entry\ObjectEntry('object', new \stdClass()),
            new Row\Entry\IntegerEntry('new_int', 1000),
            new Row\Entry\NullEntry('nothing')
        ),
    ),
    $rows
);
```

## Transformer - Cast

```php

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\Cast\CastToDateTime;
use Flow\ETL\Transformer\CastTransformer;

$entry = new StringEntry('date', '2020-01-01 00:00:00 UTC');
$transformer = new CastTransformer(new CastToDateTime('date', 'Y-m-d H:i:s.P'));
$rows = $transformer->transform(new Rows(new Row(new Row\Entries($entry))));

$this->assertInstanceOf(DateTimeEntry::class, $rows->first()->get('date'));
$this->assertSame('2020-01-01 00:00:00.+00:00', $rows->first()->valueOf('date'));
```

Casting Types: 

* `Flow\ETL\Transformer\Cast\CastToDateTime`
* `Flow\ETL\Transformer\Cast\CastToDate`
* `Flow\ETL\Transformer\Cast\CastToString`
* `Flow\ETL\Transformer\Cast\CastToInteger`
* `Flow\ETL\Transformer\Cast\CastToJson`
* `Flow\ETL\Transformer\Cast\CastToArray`
* `Flow\ETL\Transformer\Cast\CastJsonToArray`

## Transformer - EntryNameStyleConverter

This transformer requires `jawira/case-converter` in the project

```
composer require jawira/case-converter
```

```php

use Flow\ETL\Transformer\CaseConverter\CaseStyles;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\EntryNameStyleConverterTransformer;

$transformer = new EntryNameStyleConverterTransformer(CaseStyles::SNAKE);

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
            'other_case_entry_name' => 'test'
        ]
    ],
    $rows->toArray()
);
```

Supported styles: 

``` 
public const CAMEL = 'camel';
public const PASCAL = 'pascal';
public const SNAKE = 'snake';
public const ADA = 'ada';
public const MACRO = 'macro';
public const KEBAB = 'kebab';
public const TRAIN = 'train';
public const COBOL = 'cobol';
public const LOWER = 'lower';
public const UPPER = 'upper';
public const TITLE = 'title';
public const SENTENCE = 'sentence';
public const DOT = 'dot';
```

For the more details, please visit [jawira/case-converter](https://github.com/jawira/case-converter) documentation.

## Transformer - StringEntryValueCaseConverterTransformer

```php

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\StringEntryValueCaseConverterTransformer;

$transformer = StringEntryValueCaseConverterTransformer:upper('CamelCaseEntryName');

$rows = $transformer->transform(new Rows(
    Row::create(
        new Row\Entry\StringEntry('CamelCaseEntryName', 'test'),
        new Row\Entry\StringEntry('otherCaseEntryName', 'test'),
    )
));

$this->assertSame(
    [
        [
            'CamelCaseEntryName' => 'TEST',
            'otherCaseEntryName' => 'test'
        ]
    ],
    $rows->toArray()
);
```

## Transformer - ArrayKeysStyleConverter

This transformer requires `jawira/case-converter` in the project.

```
composer require jawira/case-converter
```

```php

use Flow\ETL\Transformer\CaseConverter\CaseStyles;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\ArrayKeysStyleConverterTransformer;

$transformer = new ArrayKeysStyleConverterTransformer('arrayEntry', CaseStyles::SNAKE);

$rows = $transformer->transform(new Rows(
    Row::create(
        new Row\Entry\ArrayEntry(
            'arrayEntry',
            [               
                'variantStatuses' => [
                    [
                        'statusId' => 1000,
                        'statusName' => 'NEW',
                    ],
                    [
                        'statusId' => 2000,
                        'statusName' => 'ACTIVE',
                    ],
                ],
                'variantName' => 'Variant Name'
            ],
        )
    )
));

$this->assertSame(
    [
        [
            'arrayEntry' => [
                'variant_statuses' => [
                    [
                        'status_id' => 1000,
                        'status_name' => 'NEW',
                    ],
                    [
                        'status_id' => 2000,
                        'status_name' => 'ACTIVE',
                    ],
                ],
                'variant_name' => 'Variant Name',
            ],
        ],
    ],
    $rows->toArray()
);
```

Supported styles:

``` 
public const CAMEL = 'camel';
public const PASCAL = 'pascal';
public const SNAKE = 'snake';
public const ADA = 'ada';
public const MACRO = 'macro';
public const KEBAB = 'kebab';
public const TRAIN = 'train';
public const COBOL = 'cobol';
public const LOWER = 'lower';
public const UPPER = 'upper';
public const TITLE = 'title';
public const SENTENCE = 'sentence';
public const DOT = 'dot';
```

For the more details, please visit [jawira/case-converter](https://github.com/jawira/case-converter) documentation.

## Transformer - CallbackEntry

```php
<?php

use Flow\ETL\Row;
use Flow\ETL\Row\Entry;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\CallbackEntryTransformer;

$callbackTransformer = new CallbackEntryTransformer(
    fn (Entry $entry) : Entry => new $entry(\str_replace('-', '_', $entry->name()), $entry->value())
);

$rows = $callbackTransformer->transform(
    new Rows(
        Row::create(
            new Row\Entry\IntegerEntry('old-int', 1000),
            new Entry\StringEntry('string-entry ', 'String entry')
        )
    )
);

$this->assertEquals(new Rows(
    Row::create(
        new Row\Entry\IntegerEntry('old_int', 1000),
        new Entry\StringEntry('string_entry ', 'String entry')
    )
), $rows);
```

## Transformer - ArrayExpand

This transformer takes array and expands it elements into new rows. It can take an array of anything, including 
an array of other arrays. 

```php 

use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\ArrayExpandTransformer;
use Flow\ETL\Transformer\ArrayUnpackTransformer;

$arrayExpandTransformer = new ArrayExpandTransformer('array_entry');

$rows = $arrayExpandTransformer->transform(
    new Rows(
        Row::create(
            new Row\Entry\StringEntry('string_entry', 'foo'),
            new Row\Entry\ArrayEntry('array_entry', [1, 2]),
        ),
    ),
);

$this->assertEquals(
    [
        [
            'element' => 1,
            'string_entry' => 'foo',
        ],
        [
            'element' => 2,
            'string_entry' => 'foo',
        ],
    ],
    $rows->toArray()
);
```

## Transformer - GroupToArray

Transform each row to array and group under new ArrayEntry.

This transformer is not capable to group across different `Rows`, use it only when
all `Row` elements that should be grouped are available in a single `Rows` instance.

```php

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\GroupToArrayTransformer;

$rows = new Rows(
    Row::create(
        new Row\Entry\IntegerEntry('order_id', 1),
        new Row\Entry\StringEntry('sku', 'SKU-01'),
        new Row\Entry\IntegerEntry('quantity', 1),
        new Row\Entry\FloatEntry('price', 10.00),
        new Row\Entry\StringEntry('currency', 'PLN'),
    ),
    Row::create(
        new Row\Entry\IntegerEntry('order_id', 1),
        new Row\Entry\StringEntry('sku', 'SKU-02'),
        new Row\Entry\IntegerEntry('quantity', 1),
        new Row\Entry\FloatEntry('price', 5.00),
        new Row\Entry\StringEntry('currency', 'PLN'),
    ),
    Row::create(
        new Row\Entry\IntegerEntry('order_id', 2),
        new Row\Entry\StringEntry('sku', 'SKU-01'),
        new Row\Entry\IntegerEntry('quantity', 1),
        new Row\Entry\FloatEntry('price', 10.00),
        new Row\Entry\StringEntry('currency', 'PLN'),
    )
);

$transformer = new GroupToArrayTransformer('order_id', 'order_line_items');

$this->assertSame(
    [
        [
            'order_line_items' => [
                [
                    'order_id' => 1,
                    'sku' => 'SKU-01',
                    'quantity' => 1,
                    'price' => 10.0,
                    'currency' => 'PLN',
                ],
                [
                    'order_id' => 1,
                    'sku' => 'SKU-02',
                    'quantity' => 1,
                    'price' => 5.0,
                    'currency' => 'PLN',
                ]
            ]
        ],
        [
            'order_line_items' => [
                [
                    'order_id' => 2,
                    'sku' => 'SKU-01',
                    'quantity' => 1,
                    'price' => 10.0,
                    'currency' => 'PLN'
                ]
            ]
        ]
    ],
    $transformer->transform($rows)->toArray()
);
```

## Transformer - StringFormat

```php 

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\StringFormatTransformer;

$transformer = new StringFormatTransformer('id', 'https://examlpe.com/resource/%d');

$rows = $transformer->transform(new Rows(
    new Row(new Row\Entries(new Row\Entry\IntegerEntry('id', 1))),
    new Row(new Row\Entries(new Row\Entry\IntegerEntry('id', 2))),
    new Row(new Row\Entries(new Row\Entry\IntegerEntry('id', 3))),
));

$this->assertSame(
    [
        ['id' => 'https://examlpe.com/resource/1'],
        ['id' => 'https://examlpe.com/resource/2'],
        ['id' => 'https://examlpe.com/resource/3'],
    ],
    $rows->toArray()
);
```

## Transformer - NullStringIntoNullEntry

```php 

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\NullStringIntoNullEntryTransformer;

$transformer = new NullStringIntoNullEntryTransformer('description', 'recommendation');

$rows = $transformer->transform(new Rows(
    Row::create(
        new Row\Entry\IntegerEntry('id', 1),
        new Row\Entry\BooleanEntry('active', false),
        new Row\Entry\StringEntry('name', 'NULL'),
        new Row\Entry\StringEntry('description', 'NULL'),
        new Row\Entry\StringEntry('recommendation', 'null')
    )
));

$this->assertSame(
    [[
        'id' => 1,
        'active' => false,
        'name' => 'NULL',
        'description' => null,
        'recommendation' => null,
    ]],
    $rows->toArray()
);
```

## Transformer - Clone 

```php 

use Flow\ETL\Row;
use Flow\ETL\Row\Entry;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\CloneEntryTransformer;

$rows = (new CloneEntryTransformer('id', 'id-copy'))
    ->transform(
        new Rows(
            Row::create(new Entry\IntegerEntry('id', 1))
        )
    );

$this->assertSame(
    [
        ['id' => 1, 'id-copy' => 1]
    ],
    $rows->toArray()
);
```

## Transformer - ObjectMethod

Executes method at other ObjectEntry and create new entry from the result. 
Origin ObjectEntry is not automatically removed from the Row.

```php 

use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\ObjectMethodTransformer;

$transformer = new ObjectMethodTransformer('object', 'toArray');

$rows = $transformer->transform(new Rows(
    Row::create(new Row\Entry\ObjectEntry('object', $object = new class {
        public function toArray() : array
        {
            return [
                'id' => 1,
                'name' => 'object'
            ];
        }
    }))
));

$this->assertSame(
    [
        [
            'object' => $object,
            'method_entry' => [
                'id' => 1,
                'name' => 'object'
            ]
        ]
    ],
    $rows->toArray()
);
```

## Transformer - ArrayDotGet

Read more about dot notation in [flow-php/array-dot](https://github.com/flow-php/array-dot) doumentation.

```php 

use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\ObjectMethodTransformer;

$arrayDotGetTransformer = new ArrayDotGetTransformer('array_entry', 'array.foo');

$rows = $arrayDotGetTransformer->transform(
    new Rows(
        Row::create(
            new Row\Entry\ArrayEntry('array_entry', [
                'id' => 1,
                'status' => 'PENDING',
                'enabled' => true,
                'array' => ['foo' => 'bar'],
            ]),
        ),
    )
);

$this->assertEquals(
    new Row\Entry\StringEntry('array.foo', 'bar'),
    $rows->first()->get('array.foo')
);
```

## Transformer - ArrayDotRename

Read more about dot notation in [flow-php/array-dot](https://github.com/flow-php/array-dot) doumentation.

```php 

use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\ArrayDotRenameTransformer;

$transformer = new ArrayDotRenameTransformer(
    new ArrayKeyRename('array_entry', 'array.foo', 'new_name')
);

$rows = $transformer->transform(
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
    [
        [
            'array_entry' => [
                'id' => 1,
                'status' => 'PENDING',
                'enabled' => true,
                'array' => ['new_name' => 'bar'],
            ],
        ],
    ],
    $rows->toArray()
);
```

## Transformer - StringConcat

Entries that are not StringEntry type will be skipped even if entry exists

```php 

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\StringConcatTransformer;

$transformer = new StringConcatTransformer([
    'id', 'first_name', 'last_name'
]);

$rows = $transformer->transform(new Rows(
    Row::create(
        new Row\Entry\StringEntry('id', '1'),
        new Row\Entry\StringEntry('first_name', 'Norbert'),
        new Row\Entry\StringEntry('last_name', 'Orzechowicz'),
    )
));

$this->assertSame(
    [
        [
            'id' => '1',
            'first_name' => 'Norbert',
            'last_name' => 'Orzechowicz',
            'element' => '1 Norbert Orzechowicz'
        ]
    ],
    $rows->toArray()
);
```

## Transformer - DynamicEntry 

Used to add dynamic entries to each row

```php

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\DynamicEntryTransformer;

$transformer = new DynamicEntryTransformer(
    fn (Row $row) : Row\Entries => new Row\Entries(new Row\Entry\DateTimeEntry('updated_at', new \DateTimeImmutable('2020-01-01 00:00:00 UTC')))
);

$rows = $transformer->transform(new Rows(
    Row::create(new Row\Entry\IntegerEntry('id', 1)),
    Row::create(new Row\Entry\IntegerEntry('id', 2)),
));

$this->assertSame(
    [
        ['id' => 1, 'updated_at' => '2020-01-01T00:00:00+00:00'],
        ['id' => 2, 'updated_at' => '2020-01-01T00:00:00+00:00'],
    ],
    $rows->toArray()
);
```


## Transformer - StaticEntry

Used to add static entry to each row

```php

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\StaticEntryTransformer;

$transformer = new StaticEntryTransformer(
    new StaticEntryTransformer(new Row\Entry\StringEntry('status', 'active'))
);

$rows = $transformer->transform(new Rows(
    Row::create(new Row\Entry\IntegerEntry('id', 1)),
    Row::create(new Row\Entry\IntegerEntry('id', 2)),
));

$this->assertSame(
    [
        ['id' => 1, 'status' => 'active'],
        ['id' => 2, 'status' => 'active'],
    ],
    $rows->toArray()
);
```

## Transformer - Chain

Chains many transformers into one

```php

use Flow\ETL\Transformer\ChainTransformer;
use Flow\ETL\Transformer\Condition\All;
use Flow\ETL\Transformer\Condition\EntryValueEqualsTo;
use Flow\ETL\Transformer\NewStaticEntryTransformer;

$transformer = new ChainTransformer(
    new ConditionalTransformer(
        new All(
            new EntryValueEqualsTo('first_name', 'Michael'),
            new EntryValueEqualsTo('last_name', 'Jackson'),
        ),
        new NewStaticEntryTransformer(new Row\Entry\StringEntry('profession', 'singer'))
    ),
    new ConditionalTransformer(
        new All(
            new EntryValueEqualsTo('first_name', 'Rocky'),
            new EntryValueEqualsTo('last_name', 'Balboa'),
        ),
        new NewStaticEntryTransformer(new Row\Entry\StringEntry('profession', 'boxer'))
    )
);
```

## Transformer - Clone 

Clone entries 

```php 

use Flow\ETL\Row;
use Flow\ETL\Row\Entry;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\CloneEntryTransformer;

$rows = (new CloneEntryTransformer('id', 'id-copy'))
    ->transform(
        new Rows(
            Row::create(new Entry\IntegerEntry('id', 1))
        )
    );

$this->assertSame(
    [
        ['id' => 1, 'id-copy' => 1],
    ],
    $rows->toArray()
);
```


## Transformer - MathOperation

**Warning, do not use for operations that require high precision since it's using native php arithmetic operations.**

```php 

use Flow\ETL\Row;
use Flow\ETL\Row\Entry;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\CloneEntryTransformer;

$leftEntry = new Entry\IntegerEntry('left', 5);
$rightEntry = new Entry\IntegerEntry('right', 2);

$this->assertSame(
    [
        [
            'left' => $leftEntry->value(),
            'right' => $rightEntry->value(),
            'sub' => 2.5,
        ],
    ],
    MathOperationTransformer::sub($leftEntry->name(), $rightEntry->name())
        ->transform(new Rows(Row::create($leftEntry, $rightEntry)))->toArray()
);
```

## Development

In order to install dependencies please, launch following commands:

```bash
composer install
composer install --working-dir ./tools
```

## Run Tests

In order to execute full test suite, please launch following command:

```bash
composer build
```

It's recommended to use [pcov](https://pecl.php.net/package/pcov) for code coverage however you can also use
xdebug by setting `XDEBUG_MODE=coverage` env variable.
