<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Functional;

use Spiral\Validation\ValidationInterface;

/**
 * @coversDefaultClass \Spiral\Validator\Checker\DatetimeChecker
 */
final class DatetimeCheckerTest extends BaseTestCase
{
    /**
     * @covers ::after
     */
    public function testBeforeAfterErrorMessage(): void
    {
        /** @var ValidationInterface $v */
        $v = $this->getContainer()->get(ValidationInterface::class);
        $this->assertNotNull($v);
        $res = $v->validate(
            [
                'dateBefore' => '2024-12-12',
                'dateAfter' => '2004-01-01',
            ],
            [
                'dateBefore' => [
                    ['datetime::before', 'dateAfter'],
                ],
                'dateAfter' => [
                    ['datetime::after', 'dateBefore'],
                ],
            ],
        );
        $this->assertFalse($res->isValid());
        $this->assertEquals(
            [
                'dateBefore' => 'Value dateBefore should come before value dateAfter.',
                'dateAfter' => 'Value dateAfter should come after value dateBefore.',
            ],
            $res->getErrors(),
        );
    }
}
