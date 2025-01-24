<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Unit\Fixtures;

class Check
{
    public static function check($value): bool
    {
        return false;
    }
}
