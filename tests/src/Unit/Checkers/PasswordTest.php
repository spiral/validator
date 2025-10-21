<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Unit\Checkers;

use PHPUnit\Framework\TestCase;
use Spiral\Validator\Checker\PasswordChecker;

final class PasswordTest extends TestCase
{
    private PasswordChecker $checker;

    /**
     * @return array<string, array{string, int, bool}>
     */
    public static function uppercaseDataProvider(): array
    {
        return [
            'empty string' => ['', 1, false],
            'no uppercase letters' => ['password123!', 1, false],
            'one uppercase letter - count 1' => ['Password123!', 1, true],
            'one uppercase letter - count 2' => ['Password123!', 2, false],
            'two uppercase letters - count 1' => ['PASSWORD123!', 1, true],
            'two uppercase letters - count 2' => ['PASSWORD123!', 2, true],
            'multiple uppercase letters - count 3' => ['PASSWORD123!', 3, true],
            'only uppercase letters' => ['ABCDEF', 1, true],
            'uppercase in middle' => ['passWORDtest', 1, true],
            'uppercase at end' => ['password123A', 1, true],
            'unicode uppercase' => ['passwordÀ', 1, true],
            'count zero' => ['Password', 0, true],
        ];
    }

    /**
     * @return array<string, array{string, int, bool}>
     */
    public static function lowercaseDataProvider(): array
    {
        return [
            'empty string' => ['', 1, false],
            'no lowercase letters' => ['PASSWORD123!', 1, false],
            'one lowercase letter - count 1' => ['password123!', 1, true],
            'one lowercase letter - count 2' => ['pASSWORD123!', 2, false],
            'multiple lowercase letters - count 1' => ['password123!', 1, true],
            'multiple lowercase letters - count 2' => ['password123!', 2, true],
            'only lowercase letters' => ['abcdef', 1, true],
            'lowercase in middle' => ['PASSwordTEST', 1, true],
            'lowercase at end' => ['PASSWORD123a', 1, true],
            'unicode lowercase' => ['PASSWORDà', 1, false],
            'count zero' => ['password', 0, true],
        ];
    }

    /**
     * @return array<string, array{string, int, bool}>
     */
    public static function numberDataProvider(): array
    {
        return [
            'empty string' => ['', 1, false],
            'no numbers' => ['Password!', 1, false],
            'one number - count 1' => ['Password1!', 1, true],
            'one number - count 2' => ['Password1!', 2, false],
            'multiple numbers - count 1' => ['Password123!', 1, true],
            'multiple numbers - count 2' => ['Password123!', 2, true],
            'multiple numbers - count 3' => ['Password123!', 3, true],
            'multiple numbers - count 4' => ['Password123!', 4, false],
            'only numbers' => ['123456', 1, true],
            'number at start' => ['1Password!', 1, true],
            'number at end' => ['Password!1', 1, true],
            'count zero' => ['Password1', 0, true],
        ];
    }

    /**
     * @return array<string, array{string, int, bool}>
     */
    public static function specialDataProvider(): array
    {
        return [
            'empty string' => ['', 1, false],
            'no special characters' => ['Password123', 1, false],
            'one special character - count 1' => ['Password123!', 1, true],
            'one special character - count 2' => ['Password123!', 2, false],
            'multiple special characters - count 1' => ['Password123!@#', 1, true],
            'multiple special characters - count 2' => ['Password123!@#', 2, true],
            'multiple special characters - count 3' => ['Password123!@#', 3, true],
            'multiple special characters - count 4' => ['Password123!@#', 4, false],
            'various special characters' => ['Pass@word#123$', 1, true],
            'space is special' => ['Password 123', 1, true],
            'punctuation marks' => ['Password123.,;:', 1, true],
            'symbols' => ['Password123!@#$%^&*()', 1, true],
            'brackets' => ['Password123[]{}()', 1, true],
            'unicode special' => ['Password123€', 1, false],
            'count zero' => ['Password!', 0, true],
        ];
    }

    /**
     * @covers ::uppercase
     * @dataProvider uppercaseDataProvider
     */
    public function testUppercase(string $password, int $count, bool $expected): void
    {
        self::assertSame($expected, $this->checker->uppercase($password, $count));
    }

    /**
     * @covers ::lowercase
     * @dataProvider lowercaseDataProvider
     */
    public function testLowercase(string $password, int $count, bool $expected): void
    {
        self::assertSame($expected, $this->checker->lowercase($password, $count));
    }

    /**
     * @covers ::number
     * @dataProvider numberDataProvider
     */
    public function testNumber(string $password, int $count, bool $expected): void
    {
        self::assertSame($expected, $this->checker->number($password, $count));
    }

    /**
     * @covers ::special
     * @dataProvider specialDataProvider
     */
    public function testSpecial(string $password, int $count, bool $expected): void
    {
        self::assertSame($expected, $this->checker->special($password, $count));
    }

    /**
     * @covers ::uppercase
     */
    public function testUppercaseDefaultCount(): void
    {
        self::assertTrue($this->checker->uppercase('Password'));
        self::assertFalse($this->checker->uppercase('password'));
    }

    /**
     * @covers ::lowercase
     */
    public function testLowercaseDefaultCount(): void
    {
        self::assertTrue($this->checker->lowercase('Password'));
        self::assertFalse($this->checker->lowercase('PASSWORD'));
    }

    /**
     * @covers ::number
     */
    public function testNumberDefaultCount(): void
    {
        self::assertTrue($this->checker->number('Password1'));
        self::assertFalse($this->checker->number('Password'));
    }

    /**
     * @covers ::special
     */
    public function testSpecialDefaultCount(): void
    {
        self::assertTrue($this->checker->special('Password!'));
        self::assertFalse($this->checker->special('Password'));
    }

    public function testMessages(): void
    {
        $expectedMessages = [
            'uppercase' => '[[Password must contain at least {1} uppercase letter.]]',
            'lowercase' => '[[Password must contain at least {1} lowercase letter.]]',
            'number' => '[[Password must contain at least {1} number.]]',
            'special' => '[[Password must contain at least {1} special character.]]',
        ];

        self::assertSame($expectedMessages, PasswordChecker::MESSAGES);
    }

    /**
     * @covers ::uppercase
     * @covers ::lowercase
     * @covers ::number
     * @covers ::special
     */
    public function testComplexPasswordValidation(): void
    {
        $strongPassword = 'MyStr0ng!P@ssw0rd';

        self::assertTrue($this->checker->uppercase($strongPassword));
        self::assertTrue($this->checker->lowercase($strongPassword));
        self::assertTrue($this->checker->number($strongPassword));
        self::assertTrue($this->checker->special($strongPassword));

        // Test with higher counts
        self::assertTrue($this->checker->uppercase($strongPassword, 2)); // M, S, P
        self::assertTrue($this->checker->lowercase($strongPassword, 5)); // y, t, r, g, s, s, w, r, d
        self::assertTrue($this->checker->number($strongPassword, 2)); // 0, 0
        self::assertTrue($this->checker->special($strongPassword, 2)); // !, @
    }

    /**
     * @covers ::uppercase
     * @covers ::lowercase
     * @covers ::number
     * @covers ::special
     */
    public function testWeakPasswordValidation(): void
    {
        $weakPassword = 'password';

        self::assertFalse($this->checker->uppercase($weakPassword));
        self::assertTrue($this->checker->lowercase($weakPassword));
        self::assertFalse($this->checker->number($weakPassword));
        self::assertFalse($this->checker->special($weakPassword));
    }

    #[\Override]
    protected function setUp(): void
    {
        $this->checker = new PasswordChecker();
    }
}
