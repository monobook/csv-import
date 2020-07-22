# App

**Simple products import script**

## Description

This cli command app import products from CSV file. File provides via filepath as command argument (it's mandatory).

## Env requirements

- PHP 7.3+
- MySQL 5.7
- Composer

## Install

``` bash
$ composer install
$ bin/console doctrine:database:create
$ bin/console doctrine:migrations:migrate
```

## File format requirements

CSV file should contain certain data, delimiter is a comma, enclosure is double quotes:

- SKU (string)
- description (string)
- normalPrice (float)
- specialPrice (float)

NB: You could find example file there ```data/demo.csv```

## Run

``` bash
$ bin/console app:import-products filepath
```

## Testing

``` bash
$ bin/phpunit tests
```
