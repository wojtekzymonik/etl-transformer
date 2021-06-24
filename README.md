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

## Transformer - ArrayUnpackTransformer

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

## Transformer - EntryNameCaseConverter

This transformer requires `jawira/case-converter` in the project

```
composer require jawira/case-converter
```

```php

use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\EntryNameCaseConverterTransformer;

$transformer = new EntryNameCaseConverterTransformer(EntryNameCaseConverterTransformer::STYLE_SNAKE);

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
public const STYLE_CAMEL = 'camel';
public const STYLE_PASCAL = 'pascal';
public const STYLE_SNAKE = 'snake';
public const STYLE_ADA = 'ada';
public const STYLE_MACRO = 'macro';
public const STYLE_KEBAB = 'kebab';
public const STYLE_TRAIN = 'train';
public const STYLE_COBOL = 'cobol';
public const STYLE_LOWER = 'lower';
public const STYLE_UPPER = 'upper';
public const STYLE_TITLE = 'title';
public const STYLE_SENTENCE = 'sentence';
public const STYLE_DOT = 'dot';
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

## Transformer - ArrayAccessor

Access ArrayEntry value by path in dot notation and create new entry from result.
When strictPath parameter is set to false it creates NullEntry even if path is invalid.

```php 

use Flow\ETL\Exception\RuntimeException;
use Flow\ETL\Row;
use Flow\ETL\Rows;
use Flow\ETL\Transformer\ObjectMethodTransformer;

$transformer = new ObjectMethodTransformer('object', 'toArray');

$arrayAccessorTransformer = new ArrayAccessorTransformer('array_entry', 'array.foo');

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
    )
);

$this->assertEquals(
    new Row\Entry\StringEntry('array.foo', 'bar'),
    $rows->first()->get('array.foo')
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
