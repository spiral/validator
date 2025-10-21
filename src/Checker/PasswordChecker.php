<?php

declare(strict_types=1);

namespace Spiral\Validator\Checker;

use Spiral\Core\Attribute\Singleton;
use Spiral\Validator\AbstractChecker;

/**
 * PasswordChecker provides validation rules for password strength requirements.
 *
 * This checker validates various password complexity criteria including:
 * - Uppercase letter requirements
 * - Lowercase letter requirements
 * - Numeric character requirements
 * - Special character requirements
 */
#[Singleton]
final class PasswordChecker extends AbstractChecker
{
    /**
     * Validation error messages for password rules.
     */
    public const MESSAGES = [
        'uppercase' => '[[Password must contain at least {1} uppercase letter.]]',
        'lowercase' => '[[Password must contain at least {1} lowercase letter.]]',
        'number' => '[[Password must contain at least {1} number.]]',
        'special' => '[[Password must contain at least {1} special character.]]',
    ];


    /**
     * List of special characters considered for password validation.
     *
     * Includes common special characters: spaces, punctuation, symbols, and brackets.
     */
    public const SPECIAL = [
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


    /**
     * Validates that the password contains at least the specified number of uppercase letters.
     *
     * Uses Unicode property \p{Lu} to match any uppercase letter in any language.
     *
     * @param string $value The password string to validate
     * @param positive-int $min Minimum number of uppercase letters required (default: 1)
     * @return bool True if the password meets the uppercase requirement, false otherwise
     */
    public function uppercase(string $value, int $min = 1): bool
    {
        return \preg_match_all('/\p{Lu}/u', $value) >= $min;
    }

    /**
     * Validates that the password contains at least the specified number of lowercase letters.
     *
     * Uses Unicode property \p{Ll} to match any lowercase letter in any language.
     *
     * @param string $value The password string to validate
     * @param positive-int $min Minimum number of lowercase letters required (default: 1)
     * @return bool True if the password meets the lowercase requirement, false otherwise
     */
    public function lowercase(string $value, int $min = 1): bool
    {
        return \preg_match_all('/\p{Ll}/u', $value) >= $min;
    }

    /**
     * Validates that the password contains at least the specified number of numeric digits.
     *
     * @param string $value The password string to validate
     * @param positive-int $min Minimum number of numeric digits required (default: 1)
     * @return bool True if the password meets the numeric requirement, false otherwise
     */
    public function number(string $value, int $min = 1): bool
    {
        return \preg_match_all('/[0-9]/', $value) >= $min;
    }

    /**
     * Validates that the password contains at least the specified number of special characters.
     *
     * Special characters are defined in the SPECIAL constant and include common symbols,
     * punctuation marks, and brackets.
     *
     * @param string $value The password string to validate
     * @param positive-int $min Minimum number of special characters required (default: 1)
     * @return bool True if the password meets the special character requirement, false otherwise
     */
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
