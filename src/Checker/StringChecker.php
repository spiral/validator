<?php

declare(strict_types=1);

namespace Spiral\Validator\Checker;

use Spiral\Core\Attribute\Singleton;
use Spiral\Validator\AbstractChecker;

#[Singleton]
final class StringChecker extends AbstractChecker
{
    public const MESSAGES = [
        'regexp'   => '[[Value does not match required pattern.]]',
        'shorter'  => '[[Enter text shorter or equal to {1}.]]',
        'longer'   => '[[Text must be longer or equal to {1}.]]',
        'length'   => '[[Text length must be exactly equal to {1}.]]',
        'range'    => '[[Text length should be in range of {1}-{2}.]]',
        'empty'    => '[[String value should be empty.]]',
        'notEmpty' => '[[String value should not be empty.]]',
    ];

    /**
     * Check string using regexp.
     */
    public function regexp(mixed $value, string $expression): bool
    {
        return \is_string($value) && \preg_match($expression, $value);
    }

    /**
     * Check if string length is shorter or equal that specified value.
     */
    public function shorter(mixed $value, int $length): bool
    {
        return \is_string($value) && \mb_strlen(\trim($value)) <= $length;
    }

    /**
     * Check if string length is longer or equal that specified value.
     */
    public function longer(mixed $value, int $length): bool
    {
        return \is_string($value) && \mb_strlen(\trim($value)) >= $length;
    }

    /**
     * Check if string length are equal to specified value.
     */
    public function length(mixed $value, int $length): bool
    {
        return \is_string($value) && \mb_strlen(\trim($value)) === $length;
    }

    /**
     * Check if string length are fits in specified range.
     */
    public function range(mixed $value, int $min, int $max): bool
    {
        if (!\is_string($value)) {
            return false;
        }

        $trimmed = \trim($value);

        return (\mb_strlen($trimmed) >= $min)
            && (\mb_strlen($trimmed) <= $max);
    }

    /**
     * Check string is empty
     */
    public function empty(mixed $value): bool
    {
        return \is_string($value) && '' === trim($value);
    }

    /**
     * Check string is not empty
     */
    public function notEmpty(mixed $value): bool
    {
        return \is_string($value) && '' !== trim($value);
    }
}
