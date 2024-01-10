<?php

declare(strict_types=1);

namespace Spiral\Validator\Checker;

use Spiral\Core\Attribute\Singleton;
use Spiral\Validator\AbstractChecker;

#[Singleton]
final class MixedChecker extends AbstractChecker
{
    public const MESSAGES = [
        'cardNumber' => '[[Please enter valid card number.]]',
        'match'      => '[[Fields {1} and {2} do not match.]]',
        'accepted'   => '[[Is not accepted.]]',
        'declined'   => '[[Is not declined.]]',
    ];

    /**
     * Check credit card passed by Luhn algorithm.
     *
     * @link http://en.wikipedia.org/wiki/Luhn_algorithm
     */
    public function cardNumber(mixed $value): bool
    {
        if (!\is_string($value) || \strlen($value) < 12) {
            return false;
        }

        if ($value !== \preg_replace('/\D+/', '', $value)) {
            return false;
        }

        $result = 0;
        $odd = \strlen($value) % 2;

        $length = \strlen($value);
        for ($i = 0; $i < $length; ++$i) {
            $result += $odd
                ? $value[$i]
                : (((int) $value[$i] * 2 > 9) ? (int) $value[$i] * 2 - 9 : (int) $value[$i] * 2);

            $odd = !$odd;
        }

        // Check validity.
        return $result % 10 === 0;
    }

    /**
     * Check if value matches value from another field.
     */
    public function match(mixed $value, string $field, bool $strict = false): bool
    {
        if ($strict) {
            return $value === $this->getValidator()->getValue($field, null);
        }

        return $value == $this->getValidator()->getValue($field, null);
    }

    /**
     * This is useful for validating "Terms of Service" acceptance or similar fields.
     */
    public function accepted(mixed $value): bool
    {
        return in_array($value, [true, 1, '1', 'yes', 'on'], true);
    }

    /**
     * Opposite version of `accepted`.
     */
    public function declined(mixed $value): bool
    {
        return in_array($value, [false, 0, '0', 'no', 'off'], true);
    }
}
