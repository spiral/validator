<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Functional;

use Spiral\Attributes\Bootloader\AttributesBootloader;
use Spiral\Bootloader\Security\FiltersBootloader;
use Spiral\Testing\TestCase;
use Spiral\Validation\Bootloader\ValidationBootloader;
use Spiral\Validator\Bootloader\ValidatorBootloader;

abstract class BaseTest extends TestCase
{
    public function rootDirectory(): string
    {
        return \dirname(__DIR__ . '/../../app');
    }

    public function defineBootloaders(): array
    {
        return [
            AttributesBootloader::class,
            FiltersBootloader::class,
            ValidationBootloader::class,
            ValidatorBootloader::class,
        ];
    }
}
