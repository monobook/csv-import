<?php

namespace App\Tests\Services\Parsers;

use App\Services\Parsers\CsvParser;
use Generator;
use PHPUnit\Framework\TestCase;

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
     * @dataProvider getDataProvider
     *
     * @param $file
     * @param $skipHeaders
     * @param $count
     * @param $valid
     */
    public function testParse($file, $skipHeaders, $count, $valid): void
    {
        $path = sprintf('%s/files/%s', __DIR__, $file);

        $parsedRows = [];
        $validRows = [];
        foreach ($this->instance->parse($path, $skipHeaders) as $row) {
            $parsedRows[] = $row;
            if ($row->isValid()) {
                $validRows[] = $row;
            }
        }

        self::assertCount($count, $parsedRows);
        self::assertCount($valid, $validRows);
    }

    /**
     * @return Generator
     */
    public function getDataProvider(): ?Generator
    {
        yield ['file' => 'valid.csv', 'skipHeaders' => true, 'count' => 2, 'valid' => 2];
        yield ['file' => 'valid_without_headers.csv', 'skipHeaders' => false, 'count' => 2, 'valid' => 2];
        yield ['file' => 'empty.csv', 'skipHeaders' => true, 'count' => 0, 'valid' => 0];
        yield ['file' => 'with_not_valid.csv', 'skipHeaders' => true, 'count' => 3, 'valid' => 1];
        yield ['file' => 'with_not_valid_without_headers.csv', 'skipHeaders' => false, 'count' => 4, 'valid' => 2];
    }
}
