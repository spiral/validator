<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Unit\Checkers;

use PHPUnit\Framework\TestCase;
use Spiral\Validation\ValidatorInterface;
use Spiral\Validator\Checker\MixedChecker;

final class MixedTest extends TestCase
{
    /**
     * @dataProvider cardsProvider
     * @param bool $expected
     * @param mixed $card
     */
    public function testCardNumber(bool $expected, $card): void
    {
        $checker = new MixedChecker();

        $this->assertEquals($expected, $checker->cardNumber($card));
    }

    public function testMatch(): void
    {
        $checker = new MixedChecker();

        $mock = $this->getMockBuilder(ValidatorInterface::class)->disableOriginalConstructor()->getMock();
        $mock->method('getValue')->with('abc')->willReturn(123);

        /** @var ValidatorInterface $mock */
        $this->assertTrue($checker->check($mock, 'match', 'field', 123, ['abc']));
        $this->assertFalse($checker->check($mock, 'match', 'field', 234, ['abc']));

        $this->assertTrue($checker->check($mock, 'match', 'field', '123', ['abc']));
        $this->assertFalse($checker->check($mock, 'match', 'field', '123', ['abc', true]));
    }

    public function cardsProvider(): array
    {
        return [
            [true, '122000000000003'],
            [false, '122000000010003'],
            [true, '34343434343434'],
            [false, '3434343434334'],
            [true, '5555555555554444'],
            [false, '5555 5555 5555 4444'],
            [false, '555555555554444'],
            [true, '5019717010103742'],
            [false, '5019 7170 1010 3742'],
            [false, '50197170103742'],
            [true, '36700102000000'],
            [false, '3670 0102 0000 00'],
            [false, '367001020010'],
            [true, '36148900647913'],
            [false, '36148900647933'],
            [true, '6011000400000000'],
            [false, '6011000400900000'],
            [true, '3528000700000000'],
            [false, '3528000707000000'],
            [false, 'abc'],
            [false, []],
        ];
    }

    /**
     * @dataProvider dataAccepted
     */
    public function testAccepted(mixed $value, bool $expectedResult = true): void
    {
        $checker = new MixedChecker();
        self::assertSame($expectedResult, $checker->accepted($value));
    }

    public function dataAccepted(): iterable
    {
        yield [true];
        yield [1];
        yield ['1'];
        yield ['yes'];
        yield ['on'];
        // declined values
        yield [false, false];
        yield [0, false];
        yield ['0', false];
        yield ['no', false];
        yield ['off', false];
        // invalid values
        yield [2, false];
        yield ['2', false];
        yield ['', false];
        yield ["   \n     \t      ", false];
        yield [null, false];
        yield [1.0, false];
        yield [[], false];
        yield [new \stdClass(), false];
    }

    /**
     * @dataProvider dataDeclined
     */
    public function testDeclined(mixed $value, bool $expectedResult = true): void
    {
        $checker = new MixedChecker();
        self::assertSame($expectedResult, $checker->declined($value));
    }

    public function dataDeclined(): iterable
    {
        yield [false];
        yield [0];
        yield ['0'];
        yield ['no'];
        yield ['off'];
        // accepted values
        yield [true, false];
        yield [1, false];
        yield ['1', false];
        yield ['yes', false];
        yield ['on', false];
        // invalid values
        yield [2, false];
        yield ['2', false];
        yield ['', false];
        yield ["   \n     \t      ", false];
        yield [null, false];
        yield [1.0, false];
        yield [[], false];
        yield [new \stdClass(), false];
    }
}
