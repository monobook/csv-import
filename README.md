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

If file has headers row use `--skip-headers` to skip this line.

Important: To show error messages use `-v` option

#### Memory leaks
To prevent memory leaks use `APP_ENV=prod` in .env or set `--no-debug` option for command. 

## Testing

``` bash
$ ./vendor/bin/simple-phpunit tests
```
