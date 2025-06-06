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
