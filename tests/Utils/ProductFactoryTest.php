<?php

namespace App\Tests\Utils;

use App\Model\ProductDTO;
use App\Utils\ProductFactory;
use PHPUnit\Framework\TestCase;
use TypeError;

class ProductFactoryTest extends TestCase
{
    public function testFromProductDTO(): void
    {
        $productDTO = new ProductDTO();
        $productDTO->sku = 'sku123';
        $productDTO->description = 'description';
        $productDTO->normalPrice = 12.99;
        $productDTO->specialPrice = null;

        $product = ProductFactory::fromProductDTO($productDTO);

        self::assertEquals($product->getSku(), $productDTO->sku);
        self::assertEquals($product->getDescription(), $productDTO->description);
        self::assertEquals($product->getNormalPrice(), $productDTO->normalPrice);
        self::assertEquals($product->getSpecialPrice(), $productDTO->specialPrice);
    }

    public function testFromProductDTOWithEmpty(): void
    {
        $this->expectException(TypeError::class);

        $productDTO = new ProductDTO();

        ProductFactory::fromProductDTO($productDTO);
    }

    public function testFromProductDTOWithWrongData(): void
    {
        $this->expectException(TypeError::class);

        $productDTO = new ProductDTO();
        $productDTO->sku = true;
        $productDTO->description = 1;
        $productDTO->normalPrice = 'foo';
        $productDTO->specialPrice = 'bar';

        ProductFactory::fromProductDTO($productDTO);
    }
}
