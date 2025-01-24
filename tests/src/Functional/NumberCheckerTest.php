<?php

namespace Spiral\Validator\Tests\Functional;

use Spiral\Validation\ValidationInterface;

/**
 * @coversDefaultClass \Spiral\Validator\Checker\NumberChecker
 */
final class NumberCheckerTest extends BaseTestCase
{
    /**
     * @covers ::range
     * @covers ::higher
     * @covers ::lower
     */
    public function testBeforeAfterErrorMessage(): void
    {
        /** @var ValidationInterface $v */
        $v = $this->getContainer()->get(ValidationInterface::class);
        $this->assertNotNull($v);
        $res = $v->validate(
            [
                'inRange' => 10,
                'higher' => 1,
                'lower' => 20,
            ],
            [
                'inRange' => [
                    ['number::range', 1, 5],
                ],
                'higher' => [
                    ['number::higher', 10],
                ],
                'lower' => [
                    ['number::lower', 10],
                ],
            ],
        );
        $this->assertFalse($res->isValid());
        $this->assertEquals(
            [
                'inRange' => 'Your value should be in range of 1-5.',
                'higher' => 'Your value should be equal to or higher than 10.',
                'lower' => 'Your value should be equal to or lower than 10.',
            ],
            $res->getErrors(),
        );
    }
}
