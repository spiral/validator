<?php

declare(strict_types=1);

namespace Spiral\Validator\Checker;

use Spiral\Validator\AbstractChecker;
use Spiral\Validator\CheckerInterface;
use Spiral\Validation\ValidationInterface;

class ArrayChecker extends AbstractChecker
{
    public const MESSAGES = [
        'count' => '[[Number of elements must be exactly {1}.]]',
        'longer' => '[[Number of elements must be equal to or greater than {1}.]]',
        'shorter' => '[[Number of elements must be equal to or less than {1}.]]',
        'range' => '[[Number of elements must be between {1} and {2}.]]',
        'isList' => '[[Array is not list.]]',
        'isAssoc' => '[[Array is not associative.]]',
        'expectedValues' => '[[Unexpected array value.]]',
    ];

    public function __construct(
        private ValidationInterface $validation
    ) {
    }

    public function of(mixed $value, CheckerInterface|string|array $checker): bool
    {
        if (!\is_array($value) || empty($value)) {
            return false;
        }

        foreach ($value as $item) {
            if (!$this->validation->validate(['item' => $item], ['item' => [$checker]])->isValid()) {
                return false;
            }
        }

        return true;
    }

    public function count(mixed $value, int $length): bool
    {
        if (!\is_array($value) && !$value instanceof \Countable) {
            return false;
        }

        return \count($value) === $length;
    }

    public function shorter(mixed $value, int $length): bool
    {
        if (!\is_array($value) && !$value instanceof \Countable) {
            return false;
        }

        return \count($value) <= $length;
    }

    public function longer(mixed $value, int $length): bool
    {
        if (!\is_array($value) && !$value instanceof \Countable) {
            return false;
        }

        return \count($value) >= $length;
    }

    public function range(mixed $value, int $min, int $max): bool
    {
        if (!\is_array($value) && !$value instanceof \Countable) {
            return false;
        }

        $count = \count($value);

        return $count >= $min && $count <= $max;
    }

    public function isList(mixed $value): bool
    {
        return is_array($value) && array_is_list($value);
    }

    public function isAssoc(mixed $value): bool
    {
        return is_array($value) && !array_is_list($value);
    }

    public function expectedValues(mixed $value, array $expectedValues): bool
    {
        if (!\is_array($value)) {
            return false;
        }

        return [] === array_diff($value, $expectedValues);
    }
}
