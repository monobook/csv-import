<?php

namespace App\Tests\Validator;

use App\Model\ProductDTO;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorTest extends WebTestCase
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->validator = self::$container->get('validator');

        parent::setUp();
    }

    /**
     * @dataProvider getDataProvider
     *
     * @param $sku
     * @param $description
     * @param $normalPrice
     * @param $specialPrice
     * @param $violations
     */
    public function testValid($sku, $description, $normalPrice, $specialPrice, $violations): void
    {
        $productDTO = new ProductDTO();
        $productDTO->sku = $sku;
        $productDTO->description = $description;
        $productDTO->normalPrice = $normalPrice;
        $productDTO->specialPrice = $specialPrice;

        $constraintViolationList = $this->validator->validate($productDTO);

        self::assertCount($violations, $constraintViolationList);
    }

    /**
     * @return Generator|null
     */
    public function getDataProvider(): ?Generator
    {
        // valid
        yield ['sku' => 'sku123', 'description' => 'some text', 'normalPrice' => 12.99, 'specialPrice' => null, 'violations' => 0];
        yield ['sku' => 'sku123', 'description' => 'some text', 'normalPrice' => 12.99, 'specialPrice' => 10.99, 'violations' => 0];

        // not valid
        yield ['sku' => null, 'description' => null, 'normalPrice' => null, 'specialPrice' => null, 'violations' => 3];
        yield ['sku' => 1, 'description' => 2, 'normalPrice' => 'foo', 'specialPrice' => 'bar', 'violations' => 6];
        yield ['sku' => 'sku123', 'description' => 'some text', 'normalPrice' => 12, 'specialPrice' => 10, 'violations' => 2];
        yield ['sku' => 'sku123', 'description' => 'some text', 'normalPrice' => 12.99, 'specialPrice' => 50.99, 'violations' => 1];
        yield ['sku' => 'sku123', 'description' => 'some text', 'normalPrice' => -12.99, 'specialPrice' => null, 'violations' => 1];
        yield ['sku' => 'sku123', 'description' => 'some text', 'normalPrice' => 12.99, 'specialPrice' => -10.99, 'violations' => 1];
    }
}
