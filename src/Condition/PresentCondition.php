<?php

declare(strict_types=1);

namespace Spiral\Validator\Condition;

use Spiral\Validation\ValidatorInterface;
use Spiral\Validator\AbstractCondition;

/**
 * Passes when all of the fields are explicitly provided in the request.
 */
final class PresentCondition extends AbstractCondition
{
    public function isMet(ValidatorInterface $validator, string $field, mixed $value): bool
    {
        foreach ($this->options as $option) {
            if (!$validator->hasValue($option)) {
                return false;
            }
        }

        return true;
    }
}
