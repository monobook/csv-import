<?php

namespace App\Model\Parser;

class Result
{
    /**
     * @var int
     */
    protected $created;

    /**
     * @var int
     */
    protected $updated;

    /**
     * @var int
     */
    protected $skipped;

    /**
     * @var int
     */
    protected $errors;

    protected function __construct()
    {
        $this->created = 0;
        $this->updated = 0;
        $this->skipped = 0;
        $this->errors = 0;
    }

    public function addCreated(): void
    {
        ++$this->created;
    }

    public function addUpdated(): void
    {
        ++$this->updated;
    }

    public function addSkipped(): void
    {
        ++$this->skipped;
    }

    public function addError(): void
    {
        ++$this->errors;
    }

    /**
     * @return Result
     */
    public static function create(): self
    {
        return new static();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'Product(s) created' => $this->created,
            'Product(s) updated (ie changed)' => $this->updated,
            'Product(s) skipped (ie unchanged)' => $this->skipped,
            'Row(s) with errors' => $this->errors,
        ];
    }
}
