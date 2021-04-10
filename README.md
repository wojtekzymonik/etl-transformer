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
