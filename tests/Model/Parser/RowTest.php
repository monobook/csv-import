<?php

namespace App\Tests\Model\Parser;

use App\Model\Parser\Row;
use Generator;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    public function testGetData(): void
    {
        $row = Row::create(['foo', 'bar', 0.0, 0.0], 1);

        self::assertEquals(['foo', 'bar', 0.0, 0.0], $row->getData());
        self::assertEquals(1, $row->getLine());

        $row = Row::create([], 1);

        self::assertEquals([], $row->getData());
        self::assertEquals(1, $row->getLine());
    }

    /**
     * @dataProvider getErrorsDataProvider
     *
     * @param $data
     * @param $errors
     */
    public function testGetErrors($data, $errors): void
    {
        $row = Row::create($data, 1);

        self::assertEquals($errors, $row->getErrors());
    }

    /**
     * @dataProvider getIsValidDataProvider
     *
     * @param $data
     * @param $isValid
     */
    public function testIsValid($data, $isValid): void
    {
        $row = Row::create($data, 1);

        self::assertEquals($isValid, $row->isValid());
    }

    /**
     * @return Generator
     */
    public function getErrorsDataProvider(): Generator
    {
        yield ['data' => [], 'errors' => ['Inappropriate amount of data.']];
        yield ['data' => ['', ''], 'errors' => ['Inappropriate amount of data.']];
        yield ['data' => ['', '', null, null], 'errors' => []];
        yield ['data' => null, 'errors' => ['An invalid handle is supplied.']];
        yield ['data' => false, 'errors' => ['End of file.']];
        yield ['data' => 'foo', 'errors' => ['Not valid format.']];
    }

    /**
     * @return Generator
     */
    public function getIsValidDataProvider(): Generator
    {
        yield ['data' => [], 'isValid' => false];
        yield ['data' => null, 'isValid' => false];
        yield ['data' => ['foo', 'bar', 0.0, 0, 0], 'isValid' => false];
        yield ['data' => ['foo'], 'isValid' => false];
        yield ['data' => ['foo', 'bar', 0.0, 0.0], 'isValid' => true];
    }
}
