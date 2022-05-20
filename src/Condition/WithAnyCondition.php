<?php

declare(strict_types=1);

namespace Spiral\Validator\Condition;

use Spiral\Validation\ValidatorInterface;
use Spiral\Validator\AbstractCondition;

/**
 * Fires when any of listed values are not empty.
 */
final class WithAnyCondition extends AbstractCondition
{
    public function isMet(ValidatorInterface $validator, string $field, mixed $value): bool
    {
        foreach ($this->options as $option) {
            if (!empty($validator->getValue($option))) {
                return true;
            }
        }

        return false;
    }
}
