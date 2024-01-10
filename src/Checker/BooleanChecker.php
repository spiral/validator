<?php

declare(strict_types=1);

namespace Spiral\Validator\Checker;

use Spiral\Core\Attribute\Singleton;
use Spiral\Validator\AbstractChecker;

#[Singleton]
final class BooleanChecker extends AbstractChecker
{
    public const MESSAGES = [
        'isTrue' => '[[Should be true.]]',
        'isFalse' => '[[Should be false.]]',
    ];

    public function isTrue(mixed $value): bool
    {
        return true === $value;
    }

    public function isFalse(mixed $value): bool
    {
        return false === $value;
    }
}
