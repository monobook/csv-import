<?php

namespace App\Tests\Utils;

use App\Utils\ProductDTOMapper;
use Generator;
use PHPUnit\Framework\TestCase;

class ProductDTOMapperTest extends TestCase
{
    /**
     * @dataProvider getDataProvider
     *
     * @param $data
     * @param $expected
     */
    public function testMap($data, $expected): void
    {
        $productDTO = ProductDTOMapper::map($data);

        self::assertEquals($productDTO->sku, $expected[0]);
        self::assertIsString($productDTO->sku);
        self::assertEquals($productDTO->description, $expected[1]);
        self::assertIsString($productDTO->description);
        self::assertEquals($productDTO->normalPrice, $expected[2]);
        self::assertIsFloat($productDTO->normalPrice);
        self::assertEquals($productDTO->specialPrice, $expected[3]);
        if (!empty($expected[3])) {
            self::assertIsFloat($productDTO->specialPrice);
        } else {
            self::assertNull($productDTO->specialPrice);
        }
    }

    /**
     * @return Generator
     */
    public function getDataProvider(): Generator
    {
        yield ['data' => ['foo123', 'description', 1.2, null], 'expected' => ['foo123', 'description', 1.2, null]];
        yield ['data' => ['foo123', 'description', 1.2, 1.2], 'expected' => ['foo123', 'description', 1.2, 1.2]];
        yield ['data' => [null, null, null, null], 'expected' => ['', '', 0.0, null]];
        yield ['data' => [], 'expected' => ['', '', 0.0, null]];
        yield ['data' => [null, null], 'expected' => ['', '', 0.0, null]];
        yield ['data' => [''], 'expected' => ['', '', 0.0, null]];
    }
}
