<?php

namespace App\Services\Parsers;

use App\Model\ProductDTO;
use Generator;
use RuntimeException;

class CsvParser
{
    public const SKU = 'SKU';
    public const DESCRIPTION = 'description';
    public const NORMAL_PRICE = 'normalPrice';
    public const SPECIAL_PRICE = 'specialPrice';

    public const HEADERS = [
        self::SKU,
        self::DESCRIPTION,
        self::NORMAL_PRICE,
        self::SPECIAL_PRICE,
    ];

    public const MAP = [
        self::SKU => 0,
        self::DESCRIPTION => 1,
        self::NORMAL_PRICE => 2,
        self::SPECIAL_PRICE => 3,
    ];

    /**
     * @param string $path
     * @param bool   $andClearHeaders
     *
     * @return Generator
     */
    public function parse(string $path, bool $andClearHeaders = true): Generator
    {
        if (!$this->isValid($path)) {
            throw new RuntimeException('File not valid for parse.');
        }

        $res = fopen($path, 'rb');
        if ($andClearHeaders) {
            fgetcsv($res);
        }

        while (!feof($res)) {
            $data = fgetcsv($res);
            if ($data) {
                yield $this->map($data);
            }
        }
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    protected function isValid(string $path): bool
    {
        $res = fopen($path, 'rb');

        $headers = fgetcsv($res);

        return $headers
            && count(array_intersect($headers, self::HEADERS)) === count(self::HEADERS)
            && array_keys(self::MAP) === $headers;
    }

    /**
     * @param array $data
     *
     * @return ProductDTO
     */
    protected function map(array $data): ProductDTO
    {
        $product = new ProductDTO();
        $product->sku = (string) $data[self::MAP[self::SKU]];
        $product->description = (string) $data[self::MAP[self::DESCRIPTION]];
        $product->normalPrice = (float) $data[self::MAP[self::NORMAL_PRICE]];
        $product->specialPrice = !empty($data[self::MAP[self::SPECIAL_PRICE]])
            ? (float) $data[self::MAP[self::SPECIAL_PRICE]]
            : null;

        return $product;
    }
}
