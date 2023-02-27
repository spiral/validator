<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Functional\Bootloader;

use Spiral\Validation\ValidationInterface;
use Spiral\Validation\ValidationProviderInterface;
use Spiral\Validator\Bootloader\ValidatorBootloader;
use Spiral\Validator\Config\ValidatorConfig;
use Spiral\Validator\FilterDefinition;
use Spiral\Validator\Tests\Functional\BaseTest;
use Spiral\Validator\Validation;

final class ValidatorBootloaderTest extends BaseTest
{
    public function testBootloaderRegistered(): void
    {
        $this->assertBootloaderRegistered(ValidatorBootloader::class);
    }

    public function testValidationRegistered(): void
    {
        $provider = $this->getContainer()->get(ValidationProviderInterface::class);

        $this->assertInstanceOf(Validation::class, $provider->getValidation(FilterDefinition::class));
        $this->assertContainerBoundAsSingleton(ValidationInterface::class, Validation::class);
    }

    /** @dataProvider dataHasCheckerByDefault */
    public function testHasCheckerByDefault(string $checkerName): void
    {
        $config = $this->getContainer()->get(ValidatorConfig::class);

        $this->assertTrue($config->hasChecker($checkerName));
    }

    /** @dataProvider dataHasConditionByDefault */
    public function testHasConditionByDefault(string $conditionName): void
    {
        $config = $this->getContainer()->get(ValidatorConfig::class);

        $this->assertTrue($config->hasCondition($conditionName));
    }

    public function dataHasCheckerByDefault(): \Traversable
    {
        yield ['type'];
        yield ['number'];
        yield ['mixed'];
        yield ['address'];
        yield ['string'];
        yield ['file'];
        yield ['image'];
        yield ['datetime'];
        yield ['array'];
        yield ['boolean'];
    }

    public function dataHasConditionByDefault(): \Traversable
    {
        yield ['absent'];
        yield ['present'];
        yield ['anyOf'];
        yield ['noneOf'];
        yield ['withAny'];
        yield ['withoutAny'];
        yield ['withAll'];
        yield ['withoutAll'];
    }
}
