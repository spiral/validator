<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Unit;

use Spiral\Validator\Exception\ParserException;

final class ParserTest extends BaseTest
{
    public function testClosure(): void
    {
        $validator = $this->validation->validate([
            'name' => 'string'
        ], [
            'name' => [
                static function () {
                    return false;
                }
            ]
        ]);

        $this->assertFalse($validator->isValid());
    }

    public function testParseError(): void
    {
        $this->expectException(ParserException::class);

        $validator = $this->validation->validate([
            'name' => 'string'
        ], [
            'name' => [
                []
            ]
        ]);

        $this->assertFalse($validator->isValid());
    }
}
