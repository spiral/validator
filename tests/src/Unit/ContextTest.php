<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Unit;

final class ContextTest extends BaseTestCase
{
    public function testNoRules(): void
    {
        $validator = $this->validation->validate([], [], ['context']);
        $this->assertSame(['context'], $validator->getContext());
    }
}
