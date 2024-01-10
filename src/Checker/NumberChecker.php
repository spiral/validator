<?php

declare(strict_types=1);

namespace Spiral\Validator\Checker;

use Spiral\Core\Attribute\Singleton;
use Spiral\Validator\AbstractChecker;

#[Singleton]
final class NumberChecker extends AbstractChecker
{
    public const MESSAGES = [
        'range'  => '[[Your value should be in range of {1}-{2}.]]',
        'higher' => '[[Your value should be equal to or higher than {1}.]]',
        'lower'  => '[[Your value should be equal to or lower than {1}.]]',
    ];

    /**
     * Check if number in specified range.
     */
    public function range(mixed $value, mixed $begin, mixed $end): bool
    {
        return \is_numeric($value) && $value >= $begin && $value <= $end;
    }

    /**
     * Check if value is bigger or equal that specified.
     */
    public function higher(mixed $value, mixed $limit): bool
    {
        return \is_numeric($value) && $value >= $limit;
    }

    /**
     * Check if value smaller of equal that specified.
     */
    public function lower(mixed $value, mixed $limit): bool
    {
        return \is_numeric($value) && $value <= $limit;
    }
}
