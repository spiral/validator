<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests;

class ContextTest extends BaseTest
{
    public function testNoRules(): void
    {
        $validator = $this->validation->validate([], [], ['context']);
        $this->assertSame(['context'], $validator->getContext());
    }
}
