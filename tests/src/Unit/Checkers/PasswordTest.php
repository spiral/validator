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
            'cyrillic text' => ['СлавикПароль', 2, true],
            // French text
            'french text with accents' => ['Élève Français', 2, true],
            'french uppercase only' => ['ÉLÈVE', 1, true],
            'french mixed case' => ['bonjour Monde', 1, true],
            // Spanish text
            'spanish with accents' => ['Español México', 2, true],
            'spanish uppercase only' => ['ESPAÑOL', 1, true],
            'spanish mixed case' => ['buenos Días', 1, true],
            // German text
            'german with umlauts' => ['Bräuche München', 2, true],
            'german uppercase only' => ['BRÄUCHE MÜNCHEN', 1, true],
            'german mixed case' => ['österreich Wien', 1, true],
            // Portuguese text
            'portuguese with accents' => ['Português Brasil', 2, true],
            'portuguese uppercase only' => ['PORTUGUÊS', 1, true],
            'portuguese mixed case' => ['são Paulo', 1, true],
            // Arabic text (right-to-left, no uppercase)
            'arabic text' => ['مرحبا العالم', 1, false],
            'arabic mixed with latin' => ['مرحبا WORLD', 1, true],
            // Chinese text (no case distinction)
            'chinese characters' => ['你好世界', 1, false],
            'chinese with latin uppercase' => ['你好WORLD', 1, true],
            // Japanese text
            'japanese hiragana' => ['こんにちは', 1, false],
            'japanese katakana' => ['コンニチハ', 1, false],
            'japanese mixed' => ['HELLOさようなら', 1, true],
            // Hindi text
            'hindi text' => ['नमस्ते दुनिया', 1, false],
            'hindi with latin' => ['नमस्ते HELLO', 1, true],
            // Turkish text
            'turkish with dotless i' => ['İstanbul Iğdır', 2, true],
            'turkish uppercase only' => ['İSTANBUL IĞDIR', 1, true],
            // Greek text
            'greek text' => ['Γειά σου Κόσμε', 2, true],
            'greek uppercase only' => ['ΓΕΙΑ ΣΟΥ ΚΟΣΜΕ', 1, true],
            // Korean text
            'korean hangul' => ['안녕하세요', 1, false],
            'korean with latin' => ['안녕 HELLO', 1, true],
            // Thai text
            'thai text' => ['สวัสดีครับ', 1, false],
            'thai with latin uppercase' => ['สวัสดี HELLO', 1, true],
            // Vietnamese text
            'vietnamese with diacritics' => ['Xin chào Việt Nam', 2, true],
            'vietnamese uppercase only' => ['XIN CHÀO', 1, true],
            // Italian text
            'italian with accents' => ['Città Italiana', 2, true],
            'italian uppercase only' => ['CITTÀ ITALIANA', 1, true],
            // Polish text
            'polish with diacritics' => ['Łódź Warszawa', 2, true],
            'polish uppercase only' => ['ŁÓDŹ WARSZAWA', 1, true],
            // Swedish text
            'swedish with special chars' => ['Åre Östersund', 2, true],
            'swedish uppercase only' => ['ÅRE ÖSTERSUND', 1, true],
            // Dutch text
            'dutch with ij ligature' => ['IJsselmeer Amsterdam', 2, true],
            'dutch uppercase only' => ['IJSSELMEER', 1, true],
            // Czech text
            'czech with diacritics' => ['Praha České', 2, true],
            'czech uppercase only' => ['PRAHA ČESKÉ', 1, true],
            // Romanian text
            'romanian with diacritics' => ['București România', 2, true],
            'romanian uppercase only' => ['BUCUREȘTI ROMÂNIA', 1, true],
            // Hungarian text
            'hungarian with double accents' => ['Budapest Magyarország', 2, true],
            'hungarian uppercase only' => ['BUDAPEST MAGYARORSZÁG', 1, true],
            // Belarusian text
            'belarusian text' => ['Мінск Беларусь', 2, true],
            'belarusian uppercase only' => ['МІНСК БЕЛАРУСЬ', 1, true],
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
            'unicode lowercase' => ['PASSWORDà', 1, true],
            'count zero' => ['password', 0, true],
            'cyrillic text' => ['СлавикПароль', 9, true],
            // Belarusian text
            'belarusian text' => ['Мінск Беларусь', 1, true],
            'belarusian lowercase only' => ['мінск беларусь', 1, true],
            // French text
            'french text with accents' => ['Élève Français', 1, true],
            'french lowercase only' => ['éléphant', 1, true],
            'french mixed case' => ['Bonjour Monde', 1, true],
            // Spanish text
            'spanish with accents' => ['Español México', 1, true],
            'spanish lowercase only' => ['niño español', 1, true],
            'spanish mixed case' => ['Buenos Días', 1, true],
            // German text
            'german with umlauts' => ['Bräuche München', 1, true],
            'german lowercase only' => ['größer älter', 1, true],
            'german mixed case' => ['Österreich Wien', 1, true],
            // Portuguese text
            'portuguese with accents' => ['Português Brasil', 1, true],
            'portuguese lowercase only' => ['não está', 1, true],
            'portuguese mixed case' => ['São Paulo', 1, true],
            // Chinese text (no case distinction)
            'chinese characters' => ['你好世界', 1, false],
            'chinese with latin lowercase' => ['你好world', 1, true],
            // Turkish text
            'turkish with dotless i' => ['İstanbul Iğdır', 1, true],
            'turkish lowercase only' => ['ışık çğü', 1, true],
            // Greek text
            'greek text' => ['Γειά σου Κόσμε', 1, true],
            'greek lowercase only' => ['γεια σας', 1, true],
            // Korean text
            'korean hangul' => ['안녕하세요', 1, false],
            'korean with latin' => ['Hello 안녕', 1, true],
            // Thai text
            'thai text' => ['สวัสดีครับ', 1, false],
            'thai with latin lowercase' => ['hello สวัสดี', 1, true],
            // Vietnamese text
            'vietnamese with diacritics' => ['Xin chào Việt Nam', 1, true],
            'vietnamese lowercase only' => ['xin chào', 1, true],
            // Italian text
            'italian with accents' => ['Città Italiana', 1, true],
            'italian lowercase only' => ['ciao mondo', 1, true],
            // Polish text
            'polish with diacritics' => ['Łódź Warszawa', 1, true],
            'polish lowercase only' => ['łódź warszawa', 1, true],
            // Swedish text
            'swedish with special chars' => ['Åre Östersund', 1, true],
            'swedish lowercase only' => ['åäö är bra', 1, true],
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
