<?php

namespace App\Tests\Services\Parsers;

use App\Services\Parsers\CsvParser;
use Generator;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CsvParserTest extends TestCase
{
    /**
     * @var CsvParser
     */
    protected $instance;

    protected function setUp(): void
    {
        $this->instance = new CsvParser();
    }

    /**
     * @dataProvider getValidDataProvider
     *
     * @param $file
     * @param $count
     */
    public function testParseValid($file, $count): void
    {
        $path = sprintf('%s/files/%s', __DIR__, $file);

        $products = [];
        foreach ($this->instance->parse($path) as $productDTO) {
            $products[] = $productDTO;
        }

        self::assertCount($count, $products);
    }

    /**
     * @dataProvider getNotValidDataProvider
     *
     * @param $file
     */
    public function testParseNotValid($file): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File not valid for parse');

        $path = sprintf('%s/files/%s', __DIR__, $file);

        $products = [];
        foreach ($this->instance->parse($path) as $productDTO) {
            $products[] = $productDTO;
        }
    }

    /**
     * @return Generator
     */
    public function getValidDataProvider(): ?Generator
    {
        yield ['file' => 'valid.csv', 'count' => 2];
    }

    /**
     * @return Generator
     */
    public function getNotValidDataProvider(): ?Generator
    {
        yield ['file' => 'empty.csv'];
        yield ['file' => 'not_enough.csv'];
        yield ['file' => 'not_valid.csv'];
        yield ['file' => 'not_valid_position.csv'];
    }
}
