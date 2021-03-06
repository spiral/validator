<?php

declare(strict_types=1);

namespace Spiral\Validator\Condition;

use Spiral\Validation\ValidatorInterface;
use Spiral\Validator\AbstractCondition;

/**
 * Fires when all of listed values are empty.
 */
final class WithoutAllCondition extends AbstractCondition
{
    public function isMet(ValidatorInterface $validator, string $field, mixed $value): bool
    {
        foreach ($this->options as $option) {
            if (!empty($validator->getValue($option))) {
                return false;
            }
        }

        return true;
    }
}
