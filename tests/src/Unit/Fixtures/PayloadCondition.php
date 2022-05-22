<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Unit\Fixtures;

use Spiral\Validator\AbstractCondition;
use Spiral\Validation\ValidatorInterface;

class PayloadCondition extends AbstractCondition
{
    public function isMet(ValidatorInterface $validator, string $field, $value): bool
    {
        $payload = $this->getPayload($validator)['j'];
        switch ($field) {
            case 'i':
                return $value === $payload - 1;

            case 'j':
                return $value === $payload;

            case 'k':
                return $value === $payload + 1;

            default:
                return false;
        }
    }

    /**
     * @param ValidatorInterface $validator
     *
     * @return array
     */
    protected function getPayload(ValidatorInterface $validator): array
    {
        $payload = [];
        foreach ($this->options as $option) {
            $payload[$option] = $validator->getValue(
                $option,
                $validator->getContext()[$option] ?? null
            );
        }

        return $payload;
    }
}
