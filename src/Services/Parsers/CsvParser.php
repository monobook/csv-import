<?php

namespace App\Services\Parsers;

use App\Model\Parser\Row;
use Generator;

class CsvParser
{
    /**
     * @param string $path
     * @param bool   $skipHeaders
     *
     * @return Generator
     */
    public function parse(string $path, bool $skipHeaders = true): Generator
    {
        $line = 0;

        $res = fopen($path, 'rb');
        if ($skipHeaders) {
            ++$line;

            fgetcsv($res);
        }

        while (!feof($res)) {
            yield Row::create(fgetcsv($res), ++$line);
        }
    }
}
