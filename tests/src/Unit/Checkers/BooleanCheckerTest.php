<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Unit\Checkers;

use PHPUnit\Framework\Attributes\DataProvider;
use Spiral\Validator\Checker\BooleanChecker;
use Spiral\Validator\Tests\Unit\BaseTestCase;

final class BooleanCheckerTest extends BaseTestCase
{
    public static function dataIsTrue(): iterable
    {
        yield [true, true];
        yield [false, false];
        // not boolean values
        yield [[], false];
        yield ['', false];
        yield [1, false];
        yield [2.0, false];
        yield ['foo', false];
        yield [null, false];
        yield [new \stdClass(), false];
    }

    public static function dataIsFalse(): iterable
    {
        yield [true, false];
        yield [false, true];
        // not boolean values
        yield [[], false];
        yield ['', false];
        yield [1, false];
        yield [2.0, false];
        yield ['foo', false];
        yield [null, false];
        yield [new \stdClass(), false];
    }

    #[DataProvider('dataIsTrue')]
    public function testIsAssoc(mixed $value, bool $expectedResult): void
    {
        $checker = new BooleanChecker();
        self::assertSame($expectedResult, $checker->isTrue($value));
    }

    #[DataProvider('dataIsFalse')]
    public function testIsFalse(mixed $value, bool $expectedResult): void
    {
        $checker = new BooleanChecker();
        self::assertSame($expectedResult, $checker->isFalse($value));
    }
}
