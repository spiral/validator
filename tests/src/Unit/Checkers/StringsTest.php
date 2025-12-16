<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Unit\Checkers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Spiral\Validator\Checker\StringChecker;

final class StringsTest extends TestCase
{
    public static function dataEmpty(): iterable
    {
        yield ['', true];
        yield ["   \n     \t      ", true];
        yield ['1', false];
        yield ['0', false];
        //not string
        yield [null, false];
        yield [1, false];
        yield [1.0, false];
        yield [[], false];
        yield [new \stdClass(), false];
    }

    public static function dataNotEmpty(): iterable
    {
        yield ['', false];
        yield ["   \n     \t      ", false];
        yield ['1', true];
        yield ['0', true];
        //not string
        yield [null, false];
        yield [1, false];
        yield [1.0, false];
        yield [[], false];
        yield [new \stdClass(), false];
    }

    public function testShorter(): void
    {
        $checker = new StringChecker();

        $this->assertFalse($checker->shorter('abc', 2));
        $this->assertFalse($checker->shorter('абв', 2));

        $this->assertTrue($checker->shorter('abc', 3));
        $this->assertTrue($checker->shorter('абв', 3));

        $this->assertTrue($checker->shorter('abc', 4));
        $this->assertTrue($checker->shorter('абв', 4));

        $this->assertFalse($checker->shorter(null, 4));
        $this->assertFalse($checker->shorter([], 4));
    }

    public function testLonger(): void
    {
        $checker = new StringChecker();

        $this->assertTrue($checker->longer('abc', 2));
        $this->assertTrue($checker->longer('абв', 2));

        $this->assertTrue($checker->longer('abc', 3));
        $this->assertTrue($checker->longer('абв', 3));

        $this->assertFalse($checker->longer('abc', 4));
        $this->assertFalse($checker->longer('абв', 4));

        $this->assertFalse($checker->longer(null, 4));
        $this->assertFalse($checker->longer([], 4));
    }

    public function testLength(): void
    {
        $checker = new StringChecker();

        $this->assertTrue($checker->length('abc', 3));
        $this->assertTrue($checker->length('абв', 3));

        $this->assertFalse($checker->length('abc', 5));
        $this->assertFalse($checker->length('абв', 5));

        $this->assertFalse($checker->length(null, 5));
        $this->assertFalse($checker->length([], 2));
    }

    public function testRange(): void
    {
        $checker = new StringChecker();

        $this->assertTrue($checker->range('abc', 2, 4));
        $this->assertTrue($checker->range('абв', 1, 100));

        $this->assertTrue($checker->range('abc', 0, 3));
        $this->assertTrue($checker->range('абв', 3, 20));

        $this->assertFalse($checker->range('abc', 5, 10));
        $this->assertFalse($checker->range('абв', 0, 2));

        $this->assertFalse($checker->range(null, 0, 2));
        $this->assertFalse($checker->range([], 0, 2));
    }

    public function testRegexp(): void
    {
        $checker = new StringChecker();

        $this->assertTrue($checker->regexp('abc', '/^abc$/'));
        $this->assertTrue($checker->regexp('AbCdE---', '/^ab[dEC]{3}/i'));

        $this->assertFalse($checker->regexp('cba', '/^abc$/'));
        $this->assertFalse($checker->regexp('AbCfE---', '/^ab[dEC]{3}/i'));

        $this->assertFalse($checker->regexp(null, '/^abc$/'));
        $this->assertFalse($checker->regexp([], '/^ab[dEC]{3}/i'));
    }

    /**
     * @dataProvider dpShorterBytes
     */
    public function testShorterBytes(mixed $text, int $val, bool $expected = true): void
    {
        $this->assertEquals(
            $expected,
            (new StringChecker())->shorterBytes($text, $val),
        );
    }

    public function dpShorterBytes(): iterable
    {
        yield ['abc', 2, false];
        yield ['abc', 3, true];

        yield ['абв', 2, false];
        yield ['абв', 3, false];
        yield ['абв', 4, false];
        yield ['абв', 6, true];

        yield ['😀', 2, false];
        yield ['😀', 3, false];
        yield ['😀', 4, true];

        yield [null, 0, false];
        yield [[], 0, false];
    }

    /**
     * @dataProvider dpLongerBytes
     */
    public function testLongerBytes(mixed $text, int $val, bool $expected = true): void
    {
        $this->assertEquals(
            $expected,
            (new StringChecker())->longerBytes($text, $val),
        );
    }

    public function dpLongerBytes(): iterable
    {
        yield ['a', 2, false];
        yield ['ab', 2, true];

        yield ['а', 1, true];
        yield ['а', 2, true];
        yield ['аб', 3, true];
        yield ['аб', 4, true];
        yield ['аб', 5, false];

        yield ['😀', 1, true];
        yield ['😀', 2, true];
        yield ['😀', 3, true];
        yield ['😀', 4, true];
        yield ['😀', 5, false];

        yield [null, 0, false];
        yield [[], 0, false];
    }

    /**
     * @dataProvider dpLengthBytes
     */
    public function testLengthBytes(mixed $text, int $val, bool $expected = true): void
    {
        $this->assertEquals(
            $expected,
            (new StringChecker())->lengthBytes($text, $val),
        );
    }

    public function dpLengthBytes(): iterable
    {
        yield ['a', 1, true];
        yield ['ab', 2, true];
        yield ['ab', 1, false];

        yield ['а', 1, false];
        yield ['а', 2, true];
        yield ['аб', 4, true];
        yield ['абв', 6, true];
        yield ['абв', 5, false];

        yield ['😀', 1, false];
        yield ['😀', 4, true];

        yield [null, 0, false];
        yield [[], 0, false];
    }

    /**
     * @dataProvider dbRangeBytes
     */
    public function testRangeBytes(mixed $text, int $v1, int $v2, bool $expected = true): void
    {
        $this->assertEquals(
            $expected,
            (new StringChecker())->rangeBytes($text, $v1, $v2),
        );
    }

    public function dbRangeBytes(): iterable
    {
        yield ['abc', 2, 6, true];
        yield ['abc', 0, 3, true];
        yield ['abc', 5, 10, false];

        yield ['абв', 1, 100, true];
        yield ['абв', 3, 20, true];
        yield ['абв', 0, 6, true];

        yield ['😀', 0, 2, false];
        yield ['😀', 0, 4, true];
        yield ['😀', 4, 4, true];

        yield [null, 0, 2, false];
        yield [[], 0, 2, false];
    }

    #[DataProvider('dataEmpty')]
    public function testEmpty(mixed $value, bool $expectedResult): void
    {
        self::assertSame($expectedResult, (new StringChecker())->empty($value));
    }

    #[DataProvider('dataNotEmpty')]
    public function testNotEmpty(mixed $value, bool $expectedResult): void
    {
        self::assertSame($expectedResult, (new StringChecker())->notEmpty($value));
    }
}
