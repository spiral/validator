<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Unit\Checkers;

use PHPUnit\Framework\Attributes\DataProvider;
use Spiral\Validator\Checker\ArrayChecker;
use Spiral\Validator\Tests\Unit\BaseTestCase;

final class ArrayTest extends BaseTestCase
{
    public static function dataIsList(): iterable
    {
        yield [[], true];
        yield [[1, 2, 3], true];
        yield [['a', 'b', 'c'], true];
        yield [
            [0 => 'a', 1 => 'b', 2 => 'c'],
            true,
        ];
        yield [
            ['0' => 'a', '1' => 'b', '2' => 'c'],
            true,
        ];
        yield [
            ['name' => 'name', 1, 2, 3],
            false,
        ];
        yield [
            ['name' => 'foo', 'surname' => 'bar'],
            false,
        ];

        yield ['', false];
        yield [1, false];
        yield [2.0, false];
        yield ['foo', false];
        yield [null, false];
        yield [new \stdClass(), false];
    }

    public static function dataIsAssoc(): iterable
    {
        yield [[], false];
        yield [[1, 2, 3], false];
        yield [['a', 'b', 'c'], false];
        yield [
            [0 => 'a', 1 => 'b', 2 => 'c'],
            false,
        ];
        yield [
            ['0' => 'a', '1' => 'b', '2' => 'c'],
            false,
        ];
        yield [
            ['name' => 'name', 1, 2, 3],
            true,
        ];
        yield [
            ['name' => 'foo', 'surname' => 'bar'],
            true,
        ];

        yield ['', false];
        yield [1, false];
        yield [2.0, false];
        yield ['foo', false];
        yield [null, false];
        yield [new \stdClass(), false];
    }

    public static function dataExpectedValues(): iterable
    {
        yield [[], [], true];
        // list
        yield [
            ['foo', 'bar'],
            ['foo', 'bar'],
            true,
        ];
        yield [
            ['foo'],
            ['foo', 'bar'],
            true,
        ];
        yield [
            ['bar'],
            ['foo', 'bar'],
            true,
        ];
        yield [
            [1, 2, 3],
            [1, 2, 3, 4, 5, 6, 7, 8, 9, 0],
            true,
        ];
        yield [
            ['foo', 'bar'],
            ['bar'],
            false,
        ];
        yield [
            [1, 2],
            ['bar'],
            false,
        ];
        yield 'not strict comparison' => [
            ['1', '2', '3'],
            [1, 2, 3, 4, 5, 6, 7, 8, 9, 0],
            true,
        ];

        yield [
            ['name' => 'bar', 'surname' => 'bar'],
            ['bar'],
            true,
        ];
        yield [
            ['name' => 'foo', 'surname' => 'bar'],
            ['foo', 'bar'],
            true,
        ];
        yield [
            ['name' => 'foo', 'surname' => 'baz'],
            ['foo', 'bar'],
            false,
        ];

        yield ['', [], false];
        yield [1, [], false];
        yield [2.0, [], false];
        yield ['foo', [], false];
        yield [null, [], false];
        yield [new \stdClass(), [], false];
    }

    public function testOf(): void
    {
        /** @var ArrayChecker $checker */
        $checker = $this->container->get(ArrayChecker::class);

        $this->assertTrue($checker->of([1], 'is_int'));
        $this->assertTrue($checker->of([1], 'integer'));
        $this->assertTrue($checker->of(['1'], 'is_string'));

        $this->assertFalse($checker->of(1, 'is_int'));
        $this->assertFalse($checker->of([1], 'is_string'));

        $this->assertTrue($checker->of([1, 2, 3], ['in_array', [1, 2, 3]]));
        $this->assertFalse($checker->of([5, 6, 8], ['in_array', [1, 2, 3]]));
    }

    public function testCount(): void
    {
        /** @var ArrayChecker $checker */
        $checker = $this->container->get(ArrayChecker::class);

        $this->assertFalse($checker->count('foobar', 1));
        $this->assertTrue($checker->count($this->createCountable(2), 2));
        $this->assertTrue($checker->count([1, 2], 2));
        $this->assertFalse($checker->count([1, 2], 3));
    }

    public function testLonger(): void
    {
        /** @var ArrayChecker $checker */
        $checker = $this->container->get(ArrayChecker::class);

        $this->assertFalse($checker->longer('foobar', 1));
        $this->assertTrue($checker->longer($this->createCountable(2), 1));
        $this->assertTrue($checker->longer([1, 2], 1));
        $this->assertTrue($checker->longer([1, 2], 2));
        $this->assertFalse($checker->longer([1, 2], 3));
    }

    public function testShorter(): void
    {
        /** @var ArrayChecker $checker */
        $checker = $this->container->get(ArrayChecker::class);

        $this->assertFalse($checker->shorter('foobar', 1));
        $this->assertTrue($checker->shorter($this->createCountable(2), 3));
        $this->assertTrue($checker->shorter([1, 2], 3));
        $this->assertTrue($checker->shorter([1, 2], 2));
        $this->assertFalse($checker->shorter([1, 2], 1));
    }

    public function testRange(): void
    {
        /** @var ArrayChecker $checker */
        $checker = $this->container->get(ArrayChecker::class);

        $this->assertFalse($checker->range('foobar', 1, 2));
        $this->assertTrue($checker->range($this->createCountable(2), 0, 2));
        $this->assertTrue($checker->range([1, 2], 1, 2));
        $this->assertTrue($checker->range([1, 2], 2, 3));
        $this->assertFalse($checker->range([1, 2], 0, 0));
        $this->assertFalse($checker->range([1, 2], 3, 4));
    }

    #[DataProvider('dataIsList')]
    public function testIsList(mixed $value, bool $expectedResult): void
    {
        /** @var ArrayChecker $checker */
        $checker = $this->container->get(ArrayChecker::class);
        self::assertSame($expectedResult, $checker->isList($value));
    }

    #[DataProvider('dataIsAssoc')]
    public function testIsAssoc(mixed $value, bool $expectedResult): void
    {
        /** @var ArrayChecker $checker */
        $checker = $this->container->get(ArrayChecker::class);
        self::assertSame($expectedResult, $checker->isAssoc($value));
    }

    #[DataProvider('dataExpectedValues')]
    public function testExpectedValues(mixed $value, array $expectedValues, bool $expectedResult): void
    {
        /** @var ArrayChecker $checker */
        $checker = $this->container->get(ArrayChecker::class);
        self::assertSame($expectedResult, $checker->expectedValues($value, $expectedValues));
    }

    private function createCountable(int $count): \Countable
    {
        return new class($count) implements \Countable {
            private $count;

            public function __construct(int $count)
            {
                $this->count = $count;
            }

            public function count(): int
            {
                return $this->count;
            }
        };
    }
}
