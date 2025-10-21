<?php

declare(strict_types=1);

namespace Spiral\Validator\Checker;

use Spiral\Core\Attribute\Singleton;
use Spiral\Validator\AbstractChecker;

#[Singleton]
final class PasswordChecker extends AbstractChecker
{
    public const MESSAGES = [
        'uppercase' => '[[Password must contain at least {1} uppercase letter.]]',
        'lowercase' => '[[Password must contain at least {1} lowercase letter.]]',
        'number' => '[[Password must contain at least {1} number.]]',
        'special' => '[[Password must contain at least {1} special character.]]',
    ];

    public const array SPECIAL = [
        ' ',
        '~',
        '`',
        '!',
        '@',
        '#',
        '$',
        '%',
        '^',
        '&',
        '*',
        '(',
        ')',
        '_',
        '-',
        '+',
        '=',
        '{',
        '}',
        '[',
        ']',
        '|',
        ';',
        ':',
        '"',
        '<',
        '>',
        ',',
        '.',
        '?',
        '\\',
    ];


    public function uppercase(string $value, int $min = 1): bool
    {
        return \preg_match_all('/\p{Lu}/', $value) >= $min;
    }

    public function lowercase(string $value, int $min = 1): bool
    {
        return \preg_match_all('/\p{Ll}/', $value) >= $min;
    }

    public function number(string $value, int $min = 1): bool
    {
        return \preg_match_all('/[0-9]/', $value) >= $min;
    }

    public function special(string $value, int $min = 1): bool
    {
        $cnt = 0;
        foreach (\str_split($value) as $char) {
            if (\in_array($char, self::SPECIAL, true)) {
                $cnt++;
            }
            if ($cnt >= $min) {
                return true;
            }
        }
        return false;
    }
}
