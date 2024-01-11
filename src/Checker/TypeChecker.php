<?php

declare(strict_types=1);

namespace Spiral\Validator\Checker;

use Spiral\Core\Attribute\Singleton;
use Spiral\Validator\AbstractChecker;
use Spiral\Validator\Checker\Traits\NotEmptyTrait;

#[Singleton]
final class TypeChecker extends AbstractChecker
{
    use NotEmptyTrait;

    public const MESSAGES = [
        'notNull'  => '[[This value is required.]]',
        'notEmpty' => '[[This value is required.]]',
        'boolean'  => '[[Not a valid boolean.]]',
        'datetime' => '[[Not a valid datetime.]]',
        'timezone' => '[[Not a valid timezone.]]',
    ];

    public const ALLOW_EMPTY_VALUES = ['notEmpty', 'notNull'];

    /**
     * Value should not be null.
     */
    public function notNull(mixed $value): bool
    {
        return $value !== null;
    }

    /**
     * Value has to be boolean or integer[0,1].
     */
    public function boolean(mixed $value, bool $strict = false): bool
    {
        if (\is_bool($value)) {
            return true;
        }

        return  false === $strict && (\is_numeric($value) && ($value === 0 || $value === 1));
    }
}
