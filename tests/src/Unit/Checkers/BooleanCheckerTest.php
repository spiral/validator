<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Unit\Checkers;

use Spiral\Validator\Checker\BooleanChecker;
use Spiral\Validator\Tests\Unit\BaseTest;

final class BooleanCheckerTest extends BaseTest
{
    /**
     * @dataProvider dataIsTrue
     */
    public function testIsAssoc(mixed $value, bool $expectedResult): void
    {
        $checker = new BooleanChecker();
        self::assertSame($expectedResult, $checker->isTrue($value));
    }

    public function dataIsTrue(): iterable
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

    /**
     * @dataProvider dataIsFalse
     */
    public function testIsFalse(mixed $value, bool $expectedResult): void
    {
        $checker = new BooleanChecker();
        self::assertSame($expectedResult, $checker->isFalse($value));
    }

    public function dataIsFalse(): iterable
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
}
