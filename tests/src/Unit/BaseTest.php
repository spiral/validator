<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Spiral\Core\Container;
use Spiral\Validator\Checker\AddressChecker;
use Spiral\Validator\Checker\FileChecker;
use Spiral\Validator\Checker\ImageChecker;
use Spiral\Validator\Checker\StringChecker;
use Spiral\Validator\Checker\TypeChecker;
use Spiral\Validator\Config\ValidatorConfig;
use Spiral\Validator\ParserInterface;
use Spiral\Validator\RuleParser;
use Spiral\Validator\RulesInterface;
use Spiral\Validation\ValidationInterface;
use Spiral\Validator\RulesProvider;
use Spiral\Validator\Validation;

abstract class BaseTest extends TestCase
{
    public const CONFIG = [
        'checkers' => [
            'file'    => FileChecker::class,
            'image'   => ImageChecker::class,
            'type'    => TypeChecker::class,
            'address' => AddressChecker::class,
            'string'  => StringChecker::class
        ],
        'aliases'  => [
            'notEmpty' => 'type::notEmpty',
            'email'    => 'address::email',
            'url'      => 'address::url',
            'integer'  => 'is_int',
        ],
    ];

    protected ValidationInterface $validation;
    protected Container $container;

    /**
     * @throws \Throwable
     */
    public function setUp(): void
    {
        $this->container = new Container();

        $this->container->bindSingleton(ValidationInterface::class, Validation::class);
        $this->container->bindSingleton(RulesInterface::class, RulesProvider::class);
        $this->container->bindSingleton(ParserInterface::class, RuleParser::class);

        $this->container->bind(ValidatorConfig::class, new ValidatorConfig(static::CONFIG));

        $this->validation = $this->container->get(ValidationInterface::class);
    }

    protected function assertValid(array $data, array $rules): void
    {
        $this->assertTrue(
            $this->validation->validate($data, $rules)->isValid(),
            'Validation FAILED'
        );
    }

    protected function assertNotValid(string $error, array $data, array $rules): void
    {
        $validator = $this->validation->validate($data, $rules);

        $this->assertFalse($validator->isValid(), 'Validation PASSED');
        $this->assertArrayHasKey($error, $validator->getErrors());
    }
}
