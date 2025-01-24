<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Functional\Bootloader;

use PHPUnit\Framework\Attributes\DataProvider;
use Spiral\Validation\ValidationInterface;
use Spiral\Validation\ValidationProviderInterface;
use Spiral\Validator\Bootloader\ValidatorBootloader;
use Spiral\Validator\Config\ValidatorConfig;
use Spiral\Validator\FilterDefinition;
use Spiral\Validator\Tests\Functional\BaseTestCase;
use Spiral\Validator\Validation;

final class ValidatorBootloaderTest extends BaseTestCase
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

    #[DataProvider('dataHasCheckerByDefault')]
    public function testHasCheckerByDefault(string $checkerName): void
    {
        $config = $this->getContainer()->get(ValidatorConfig::class);

        $this->assertTrue($config->hasChecker($checkerName));
    }

    #[DataProvider('dataHasConditionByDefault')]
    public function testHasConditionByDefault(string $conditionName): void
    {
        $config = $this->getContainer()->get(ValidatorConfig::class);

        $this->assertTrue($config->hasCondition($conditionName));
    }

    public static function dataHasCheckerByDefault(): \Traversable
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

    public static function dataHasConditionByDefault(): \Traversable
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
