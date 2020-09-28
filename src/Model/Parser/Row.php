<?php

namespace App\Model\Parser;

class Row
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

    /**
     * @var int
     */
    protected $line;

    /**
     * @var array|bool|null
     */
    protected $data;

    /**
     * @var array
     */
    protected $errors;

    /**
     * @param array|bool|null $data
     * @param int             $line
     */
    protected function __construct($data, int $line)
    {
        $this->line = $line;
        $this->data = $data;
        $this->errors = [];

        $this->validate();
    }

    /**
     * @param array|bool|null $data
     * @param int             $line
     *
     * @return static
     */
    public static function create($data, int $line): self
    {
        return new static($data, $line);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return 0 === count($this->errors);
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return array|bool|null
     */
    public function getData()
    {
        return $this->data;
    }

    protected function validate(): void
    {
        if (is_null($this->data)) {
            $this->errors[] = 'An invalid handle is supplied.';

            return;
        }

        if (is_bool($this->data)) {
            $this->errors[] = 'End of file.';

            return;
        }

        if (is_array($this->data)) {
            if (count($this->data) !== count(self::HEADERS)) {
                $this->errors[] = 'Inappropriate amount of data.';
            }

            return;
        }

        $this->errors[] = 'Not valid format.';
    }
}
