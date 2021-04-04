# ETL Adapter: Filter

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)

## Description

ETL Adapter that provides Loaders that can filter rows.

## Transformer - FilterRows

```php 
<?php

use Flow\ETL\Adapter\Filter\EntryEqualsTo;
use Flow\ETL\Adapter\FilterRows;
use Flow\ETL\Row;
use Flow\ETL\Rows;

$transformer = new FilterRows(
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