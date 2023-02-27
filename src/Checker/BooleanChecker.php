<?php

declare(strict_types=1);

namespace Spiral\Validator\Checker;

use Spiral\Core\Container\SingletonInterface;
use Spiral\Validator\AbstractChecker;

final class BooleanChecker extends AbstractChecker implements SingletonInterface
{
    public const MESSAGES = [
        'isTrue' => '[[Should be true]]',
        'isFalse' => '[[Should be false]]',
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
