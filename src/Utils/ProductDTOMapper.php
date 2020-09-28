<?php

namespace App\Utils;

use App\Model\Parser\Row;
use App\Model\ProductDTO;

class ProductDTOMapper
{
    public const MAP = [
        Row::SKU => 0,
        Row::DESCRIPTION => 1,
        Row::NORMAL_PRICE => 2,
        Row::SPECIAL_PRICE => 3,
    ];

    /**
     * @param array $data
     *
     * @return ProductDTO
     */
    public static function map(array $data): ProductDTO
    {
        $product = new ProductDTO();

        $product->sku = !empty($data[self::MAP[Row::SKU]])
            ? (string) $data[self::MAP[Row::SKU]]
            : '';

        $product->description = !empty($data[self::MAP[Row::DESCRIPTION]])
            ? (string) $data[self::MAP[Row::DESCRIPTION]]
            : '';

        $product->normalPrice = !empty($data[self::MAP[Row::NORMAL_PRICE]])
            ? (float) $data[self::MAP[Row::NORMAL_PRICE]]
            : 0.0;

        $product->specialPrice = !empty($data[self::MAP[Row::SPECIAL_PRICE]])
            ? (float) $data[self::MAP[Row::SPECIAL_PRICE]]
            : null;

        return $product;
    }
}
