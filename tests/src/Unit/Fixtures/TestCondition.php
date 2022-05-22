<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Unit\Fixtures;

use Spiral\Validator\AbstractCondition;
use Spiral\Validation\ValidatorInterface;

class TestCondition extends AbstractCondition
{
    public function isMet(ValidatorInterface $validator, string $field, $value): bool
    {
        return true;
    }
}
