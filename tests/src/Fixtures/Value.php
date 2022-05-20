<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Fixtures;

class Value
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
