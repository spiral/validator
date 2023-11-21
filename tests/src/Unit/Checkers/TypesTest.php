<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Unit\Checkers;

use PHPUnit\Framework\TestCase;
use Spiral\Validator\Checker\TypeChecker;

final class TypesTest extends TestCase
{
    public function testNotNull(): void
    {
        $checker = new TypeChecker();

        $this->assertTrue($checker->notNull('value'));
        $this->assertTrue($checker->notNull(1));
        $this->assertTrue($checker->notNull(0));
        $this->assertTrue($checker->notNull('0'));
        $this->assertTrue($checker->notNull(''));
        $this->assertTrue($checker->notNull([]));

        $this->assertTrue($checker->notNull(false));
        $this->assertTrue($checker->notNull(true));
        $this->assertTrue($checker->notNull(new \stdClass()));
        $this->assertFalse($checker->notNull(null));
    }

    public function testNotEmpty(): void
    {
        $checker = new TypeChecker();

        $this->assertEquals(!empty('value'), $checker->notEmpty('value'));
        $this->assertEquals(!empty(1), $checker->notEmpty(1));
        $this->assertEquals(!empty(0), $checker->notEmpty(0));
        $this->assertEquals(!empty('0'), $checker->notEmpty('0'));
        $this->assertEquals(!empty(''), $checker->notEmpty(''));
        $this->assertEquals(!empty([]), $checker->notEmpty([]));

        $this->assertEquals(!empty(false), $checker->notEmpty(false));
        $this->assertEquals(!empty(true), $checker->notEmpty(true));
    }

    public function testNotEmptyStrings(): void
    {
        $checker = new TypeChecker();

        $this->assertTrue($checker->notEmpty('abc'));
        $this->assertTrue($checker->notEmpty(' ', false));

        $this->assertFalse($checker->notEmpty(' '));
        $this->assertFalse($checker->notEmpty(' ', true));
    }

    public function testBoolean(): void
    {
        $checker = new TypeChecker();

        $this->assertTrue($checker->boolean(true));
        $this->assertTrue($checker->boolean(false));
        $this->assertTrue($checker->boolean(1));
        $this->assertTrue($checker->boolean(0));

        $this->assertFalse($checker->boolean('true'));
        $this->assertFalse($checker->boolean('false'));
        $this->assertFalse($checker->boolean('0'));
        $this->assertFalse($checker->boolean('1'));
    }

    public function testBooleanStrict(): void
    {
        $checker = new TypeChecker();

        $this->assertTrue($checker->boolean(true, true));
        $this->assertTrue($checker->boolean(false, true));

        $this->assertFalse($checker->boolean(1, true));
        $this->assertFalse($checker->boolean(0, true));
        $this->assertFalse($checker->boolean('true', true));
        $this->assertFalse($checker->boolean('false', true));
        $this->assertFalse($checker->boolean('0', true));
        $this->assertFalse($checker->boolean('1', true));
    }
}
