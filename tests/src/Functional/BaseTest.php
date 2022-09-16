<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Functional;

use Spiral\Attributes\Factory;
use Spiral\Attributes\ReaderInterface;
use Spiral\Testing\TestCase;
use Spiral\Validation\Bootloader\ValidationBootloader;
use Spiral\Validator\App\Bootloader\FiltersBootloader;
use Spiral\Validator\Bootloader\ValidatorBootloader;

abstract class BaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->getContainer()->bind(ReaderInterface::class, static fn (Factory $factory) => $factory->create());
    }

    public function rootDirectory(): string
    {
        return \dirname(__DIR__ . '/../../app');
    }

    public function defineBootloaders(): array
    {
        return [
            FiltersBootloader::class,
            ValidationBootloader::class,
            ValidatorBootloader::class,
        ];
    }
}
