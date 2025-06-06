<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Unit;

use Spiral\Validator\Condition\AbsentCondition;
use Spiral\Validator\Condition\PresentCondition;
use Spiral\Validator\Condition\WithAllCondition;
use Spiral\Validator\Condition\WithAnyCondition;
use Spiral\Validator\Condition\WithoutAllCondition;
use Spiral\Validator\Condition\WithoutAnyCondition;

final class AliasedConditionsTest extends BaseTestCase
{
    public const CONFIG = [
        'checkers'   => [],
        'conditions' => [],
        'aliases'    => [
            'absent'     => AbsentCondition::class,
            'present'    => PresentCondition::class,
            'withAny'    => WithAnyCondition::class,
            'withoutAny' => WithoutAnyCondition::class,
            'withAll'    => WithAllCondition::class,
            'withoutAll' => WithoutAllCondition::class,
        ],
    ];

    public function testAbsent(): void
    {
        $this->assertValid(
            ['i' => true],
            ['i' => [['is_bool', 'if' => ['absent' => ['b']]]]],
        );

        $this->assertValid(
            ['i' => 'a', 'b' => 1],
            ['i' => [['is_bool', 'if' => ['absent' => ['b']]]]],
        );

        $this->assertNotValid(
            'i',
            ['i' => 'text'],
            ['i' => [['is_bool', 'if' => ['absent' => ['b']]]]],
        );
    }

    public function testPresent(): void
    {
        $this->assertValid(
            ['i' => true],
            ['i' => [['is_bool', 'if' => ['present' => ['i']]]]],
        );

        $this->assertNotValid(
            'i',
            ['i' => 'a', 'b' => 1],
            ['i' => [['is_bool', 'if' => ['present' => ['b']]]]],
        );

        $this->assertValid(
            ['b' => 'a'],
            ['i' => [['is_numeric', 'if' => ['present' => ['i']]]]],
        );

        $this->assertNotValid(
            'i',
            ['i' => ''],
            ['i' => [['is_numeric', 'if' => ['present' => ['i']]]]],
        );
    }

    public function testWithAny(): void
    {
        $this->assertValid(
            ['i' => 'a',],
            ['i' => [['is_bool', 'if' => ['withAny' => ['b', 'c']]]]],
        );

        $this->assertNotValid(
            'i',
            ['i' => 'a', 'b' => 'b'],
            ['i' => [['is_bool', 'if' => ['withAny' => ['b', 'c']]]]],
        );

        $this->assertNotValid(
            'i',
            ['i' => 'a', 'b' => 'b', 'c' => 'c'],
            ['i' => [['is_bool', 'if' => ['withAny' => ['b', 'c']]]]],
        );
    }

    public function testWithAll(): void
    {
        $this->assertValid(
            ['i' => 'a',],
            ['i' => [['is_bool', 'if' => ['withAll' => ['b', 'c']]]]],
        );

        $this->assertValid(
            ['i' => 'a', 'b' => 'b'],
            ['i' => [['is_bool', 'if' => ['withAll' => ['b', 'c']]]]],
        );

        $this->assertNotValid(
            'i',
            ['i' => 'a', 'b' => 'b', 'c' => 'c'],
            ['i' => [['is_bool', 'if' => ['withAll' => ['b', 'c']]]]],
        );
    }

    public function testWithoutAny(): void
    {
        $this->assertNotValid(
            'i',
            ['i' => 'a',],
            ['i' => [['is_bool', 'if' => ['withoutAny' => ['b', 'c']]]]],
        );

        $this->assertNotValid(
            'i',
            ['i' => 'a', 'b' => 'b'],
            ['i' => [['is_bool', 'if' => ['withoutAny' => ['b', 'c']]]]],
        );

        $this->assertValid(
            ['i' => 'a', 'b' => 'b', 'c' => 'c'],
            ['i' => [['is_bool', 'if' => ['withoutAny' => ['b', 'c']]]]],
        );
    }

    public function testWithoutAll(): void
    {
        $this->assertNotValid(
            'i',
            ['i' => 'a',],
            ['i' => [['is_bool', 'if' => ['withoutAll' => ['b', 'c']]]]],
        );

        $this->assertValid(
            ['i' => 'a', 'b' => 'b'],
            ['i' => [['is_bool', 'if' => ['withoutAll' => ['b', 'c']]]]],
        );

        $this->assertValid(
            ['i' => 'a', 'b' => 'b', 'c' => 'c'],
            ['i' => [['is_bool', 'if' => ['withoutAll' => ['b', 'c']]]]],
        );
    }
}
