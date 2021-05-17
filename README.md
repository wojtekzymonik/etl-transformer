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
