<?php

namespace App\Utils;

use App\Entity\Product;
use App\Model\ProductDTO;

class ProductFactory
{
    /**
     * @param ProductDTO $productDTO
     *
     * @return Product
     */
    public static function fromProductDTO(ProductDTO $productDTO): Product
    {
        $product = new Product();
        $product->setSku($productDTO->sku);
        $product->setDescription($productDTO->description);
        $product->setNormalPrice($productDTO->normalPrice);
        $product->setSpecialPrice($productDTO->specialPrice);

        return $product;
    }
}
