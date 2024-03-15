<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Unit\Checkers;

use PHPUnit\Framework\TestCase;
use Spiral\Validation\ValidatorInterface;
use Spiral\Validator\Checker\DatetimeChecker;

final class DatetimeTest extends TestCase
{
    /**
     * @dataProvider nowProvider
     * @param bool $expected
     * @param      $now
     * @param      $value
     * @param bool $orNow
     * @param bool $useMicroseconds
     */
    public function testNow(bool $expected, $now, $value, bool $orNow, bool $useMicroseconds): void
    {
        $checker = new DatetimeChecker($now);

        $this->assertSame($expected, $checker->future($value, $orNow, $useMicroseconds));
    }

    public function nowProvider(): iterable
    {
        $now = new \DateTime();
        $callableNow = static function () use ($now) {
            return $now;
        };

        yield from [
            [false, $callableNow, $now, false, true],
            [true, $callableNow, $now, true, true]
        ];

        $callableFutureTime = static function () {
            return time() + 1000;
        };
        yield from [
            [false, $callableFutureTime, $now, false, true],
            [false, $callableFutureTime, $now, true, true],
        ];

        $callablePastTime = static function () {
            return time() - 1000;
        };
        yield from [
            [true, $callablePastTime, $now, false, true],
            [true, $callablePastTime, $now, true, true],
        ];

        return [
            [false, 'tomorrow + 2hours', $now, false, true],
            [false, 'tomorrow + 2hours', $now, true, true],
            [true, 'yesterday - 2hours', $now, false, true],
            [true, 'yesterday - 2hours', $now, true, true],
            [false, $now, $now, false, true],
            [true, $now, $now, true, true],
        ];
    }

    /**
     * @dataProvider futureProvider
     *
     * @param bool  $expected
     * @param mixed $value
     * @param bool  $orNow
     * @param bool  $useMicroseconds
     */
    public function testFuture(bool $expected, $value, bool $orNow, bool $useMicroseconds): void
    {
        $value = $value instanceof \Closure ? $value() : $value;

        $checker = new DatetimeChecker();

        $this->assertSame($expected, $checker->future($value, $orNow, $useMicroseconds));
    }

    /**
     * @return array
     */
    public function futureProvider(): array
    {
        return [
            //the date is 100% in the future
            [true, $this->inFuture(1000), false, false],
            [true, $this->inFuture(1000), true, false],
            [true, $this->inFuture(1000), false, true],
            [true, $this->inFuture(1000), true, true],

            [true, 'tomorrow + 2hours', false, false],
            [true, 'now + 1000 seconds', false, false],

            // the "now" date can differ in ms
            [false, 'now', false, false],
            [false, 'now', false, true], //the threshold date comes a little bit later (in ms)
            [true, 'now', true, false],
            [false, 'now', true, true], //the threshold date comes a little bit later (in ms)

            //the date is invalid, don't check after this
            [false, [], false, false],
            [false, [], true, false],
            [false, [], false, true],
            [false, [], true, true],

            [false, $this->inPast(1000), false, false],
            [false, '', false, false],
            [false, 0, false, false],
            [false, 1.1, false, false],
            [false, false, false, false],
            [false, true, false, false],
            [false, null, false, false],
            [false, [], false, false],
            [false, new \stdClass(), false, false],
        ];
    }

    /**
     * @dataProvider pastProvider
     * @param bool  $expected
     * @param mixed $value
     * @param bool  $orNow
     * @param bool  $useMicroseconds
     */
    public function testPast(bool $expected, $value, bool $orNow, bool $useMicroseconds): void
    {
        $value = $value instanceof \Closure ? $value() : $value;

        $checker = new DatetimeChecker();

        $this->assertSame($expected, $checker->past($value, $orNow, $useMicroseconds));
    }

    /**
     * @return array
     */
    public function pastProvider(): array
    {
        return [
            //the date is 100% in the past
            [true, $this->inPast(1000), false, false],
            [true, $this->inPast(1000), true, false],
            [true, $this->inPast(1000), false, true],
            [true, $this->inPast(1000), true, true],

            [true, 'yesterday -2hours', false, false],
            [true, 'now - 1000 seconds', false, false],

            //the "now" date can differ in ms
            [false, 'now', false, false],
            [true, 'now', false, true], //the threshold date comes a little bit later (in ms)
            [true, 'now', true, false],
            [true, 'now', true, true], //the threshold date comes a little bit later (in ms)

            [false, $this->inFuture(1000), false, false],
            [true, '', false, false],
            [true, 0, false, false],
            [true, 1.1, false, false],
            [false, [], false, false],
            [false, false, false, false],
            [false, true, false, false],
            [false, null, false, false],
            [false, [], false, false],
            [false, new \stdClass(), false, false],
        ];
    }

    /**
     * @dataProvider formatProvider
     * @param bool   $expected
     * @param mixed  $value
     * @param string $format
     */
    public function testFormat(bool $expected, $value, string $format): void
    {
        $checker = new DatetimeChecker();

        $this->assertSame($expected, $checker->format($value, $format));
    }

    /**
     * @return array
     */
    public function formatProvider(): array
    {
        return [
            [true, '2019-12-27T14:27:44+00:00', 'c'], //this one is converted using other format chars
            [true, '2019-12-27T14:27:44+00:00', 'Y-m-d\TH:i:sT'], //like the 'c' one
            [true, 'Wed, 02 Oct 19 08:00:00 EST', \DateTime::RFC822],
            [true, 'Wed, 02 Oct 19 08:00:00 +0200', \DateTime::RFC822],
            [true, '2019-12-12', 'Y-m-d'],
            [true, '2019-12-12', 'Y-d-m'],
            [true, '2019-13-12', 'Y-m-d'],
            [true, '2019-12-13', 'Y-d-m'],
            [true, '2019-12-Nov', 'Y-d-M'],
            [true, '2019-12-Nov', 'Y-m-\N\o\v'],
            [false, '2019-12-Nov', 'Y-M-d'],
            [false, '2019-12-Nov', '123'],
            [false, '2019+12-Nov', 'Y-m-d'],
            [false, '-2019-12-Nov', 'Y-m-d'],
            [false, '2019-12-Abc', 'Y-d-M'],
        ];
    }

    /**
     * @dataProvider validProvider
     * @param bool  $expected
     * @param mixed $value
     */
    public function testValid(bool $expected, $value): void
    {
        $checker = new DatetimeChecker();

        $this->assertSame($expected, $checker->valid($value));
    }

    /**
     * @return array
     */
    public function validProvider(): iterable
    {
        yield [true, time() - 1000];
        yield [true, time()];
        yield [true, date('u')];
        yield [true, time() + 1000];
        yield [true, ''];
        yield [true, 'tomorrow +2hours'];
        yield [true, 'yesterday -2hours'];
        yield [true, 'now'];
        yield [true, 'now + 1000 seconds'];
        yield [true, 'now - 1000 seconds'];
        yield [true, 0];
        yield [true, 1.1];
        yield [false, []];
        yield [false, false];
        yield [false, true];
        yield [false, null];
        yield [false, []];
        yield [false, new \stdClass()];

        yield 'ATOM format' => [
            true,
            '2005-08-15T15:52:01+00:00',
        ];
        yield 'W3C format' => [
            true,
            '2005-08-15T15:52:01+00:00',
        ];
        yield 'COOKIE format' => [
            true,
            'Monday, 15-Aug-2005 15:52:01 UTC',
        ];
        yield 'RFC822 format' => [
            true,
            'Mon, 15 Aug 05 15:52:01 +0000',
        ];
        yield 'RFC1036 format' => [
            true,
            'Mon, 15 Aug 05 15:52:01 +0000',
        ];
        yield 'RFC850 format' => [
            true,
            'Monday, 15-Aug-05 15:52:01 UTC',
        ];
        yield 'RFC1123 format' => [
            true,
            'Mon, 15 Aug 2005 15:52:01 +0000',
        ];
        yield 'RFC7231 format' => [
            true,
            'Sat, 30 Apr 2016 17:52:13 GMT',
        ];
        yield 'RFC2822 format' => [
            true,
            'Mon, 15 Aug 2005 15:52:01 +0000',
        ];
        yield 'RFC3339_EXTENDED format' => [
            true,
            '2005-08-15T15:52:01.000+00:00',
        ];
        yield 'RSS format' => [
            true,
            'Mon, 15 Aug 2005 15:52:01 +0000',
        ];
        if (PHP_VERSION_ID >= 80200) {
            yield 'ISO8601_EXPANDED format' => [
                true,
                '2005-08-15T15:52:01+0000',
            ];
        }

        yield 'invalid datetime string' => [
            false,
            'you shall not pass',
        ];

        yield 'invalid numeric string' => [
            false,
            '2222222222222222222222222222222222222222222222222222222222222222',
        ];

        yield 'invalid integer' => [
            false,
            1111111111111111111111111111111111111111111111111111111111111111111111,
        ];

        yield 'scientific notation str' => [
            false,
            '1.23e-09',
        ];

        yield 'scientific notation num' => [
            false,
            1.23e-09,
        ];
    }

    public function testTimezone(): void
    {
        $checker = new DatetimeChecker();

        foreach (\DateTimeZone::listIdentifiers() as $identifier) {
            $this->assertTrue($checker->timezone($identifier));
            $this->assertFalse($checker->timezone(str_rot13($identifier)));
        }

        $this->assertFalse($checker->timezone('Any zone'));
    }

    /**
     * @dataProvider beforeProvider
     * @param bool  $expected
     * @param mixed $value
     * @param mixed $threshold
     * @param bool  $orEquals
     * @param bool  $useMicroseconds
     */
    public function testBefore(bool $expected, $value, $threshold, bool $orEquals, bool $useMicroseconds): void
    {
        $value = $value instanceof \Closure ? $value() : $value;

        $checker = new DatetimeChecker();

        $mock = $this->getMockBuilder(ValidatorInterface::class)->disableOriginalConstructor()->getMock();
        $mock->method('getValue')->with('threshold')->willReturn($threshold);

        /** @var ValidatorInterface $mock */
        $this->assertSame(
            $expected,
            $checker->check(
                $mock,
                'before',
                'field',
                $value,
                ['threshold', $orEquals, $useMicroseconds]
            )
        );
    }

    /**
     * @return array
     */
    public function beforeProvider(): array
    {
        return [
            //the date is 100% in the past
            [true, $this->inPast(1000), 'now', false, false],
            [true, $this->inPast(1000), 'now', true, false],
            [true, $this->inPast(1000), 'now', false, true],
            [true, $this->inPast(1000), 'now', true, true],

            [true, 'yesterday -2hours', 'now', false, false],
            [true, 'now - 1000 seconds', 'now', false, false],
            [true, 'now + 1000 seconds', 'tomorrow', false, false],

            //the "now" date can differ in ms
            [false, 'now', 'now', false, false],
            [true, 'now', 'now + 1000 second', false, false],
            [true, 'now', 'now', false, true], //the threshold date comes a little bit later (in ms)
            [true, 'now', 'now', true, false],
            [true, 'now', 'now', true, true], //the threshold date comes a little bit later (in ms)

            [false, $this->inFuture(1000), 'now', false, false],
            [true, '', 'now', false, false],
            [true, 0, 'now', false, false],
            [true, 1.1, 'now', false, false],
            [false, [], 'now', false, false],
            [false, false, 'now', false, false],
            [false, true, 'now', false, false],
            [false, null, 'now', false, false],
            [false, [], 'now', false, false],
            [false, new \stdClass(), 'now', false, false],
        ];
    }

    /**
     * @dataProvider afterProvider
     * @param bool  $expected
     * @param mixed $value
     * @param mixed $threshold
     * @param bool  $orEquals
     * @param bool  $useMicroseconds
     */
    public function testAfter(bool $expected, $value, $threshold, bool $orEquals, bool $useMicroseconds): void
    {
        $value = $value instanceof \Closure ? $value() : $value;

        $checker = new DatetimeChecker();

        $mock = $this->getMockBuilder(ValidatorInterface::class)->disableOriginalConstructor()->getMock();
        $mock->method('getValue')->with('threshold')->willReturn($threshold);

        /** @var ValidatorInterface $mock */
        $this->assertSame(
            $expected,
            $checker->check(
                $mock,
                'after',
                'field',
                $value,
                ['threshold', $orEquals, $useMicroseconds]
            )
        );
    }

    /**
     * @return array
     */
    public function afterProvider(): array
    {
        return [
            [true, $this->inFuture(1000), 'now', false, false],
            [true, $this->inFuture(1000), 'now', true, false],
            [true, $this->inFuture(1000), 'now', false, true],
            [true, $this->inFuture(1000), 'now', true, true],

            [true, 'tomorrow +2hours', 'now', false, false],
            [true, 'now + 1000 seconds', 'now', false, false],
            [true, 'now - 1000 seconds', 'yesterday', false, false],

            //the "now" date can differ in ms
            [false, 'now', 'now', false, false],
            [true, 'now', 'now - 1000 second', false, false],
            [false, 'now', 'now', false, true], //the threshold date comes a little bit later (in ms)
            [true, 'now', 'now', true, false],
            [false, 'now', 'now', true, true], //the threshold date comes a little bit later (in ms)

            [false, $this->inPast(1000), 'now', false, false],
            [false, '', 'now', false, false],
            [false, 0, 'now', false, false],
            [false, 1.1, 'now', false, false],
            [false, [], 'now', false, false],
            [false, false, 'now', false, false],
            [false, true, 'now', false, false],
            [false, null, 'now', false, false],
            [false, [], 'now', false, false],
            [false, new \stdClass(), 'now', false, false],
        ];
    }

    private function now(): \Closure
    {
        return static function () {
            return \time();
        };
    }

    private function inFuture(int $seconds): \Closure
    {
        return static function () use ($seconds) {
            return \time() + $seconds;
        };
    }

    private function inPast(int $seconds): \Closure
    {
        return static function () use ($seconds) {
            return \time() - $seconds;
        };
    }
}
