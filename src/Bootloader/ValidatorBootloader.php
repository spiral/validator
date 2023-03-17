<?php

declare(strict_types=1);

namespace Spiral\Validator\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Config\Patch\Append;
use Spiral\Tokenizer\Bootloader\TokenizerBootloader;
use Spiral\Validation\Bootloader\ValidationBootloader;
use Spiral\Validation\ValidationInterface;
use Spiral\Validation\ValidationProvider;
use Spiral\Validator\Condition;
use Spiral\Validator\Checker;
use Spiral\Validator\Config\ValidatorConfig;
use Spiral\Validator\FilterDefinition;
use Spiral\Validator\RulesInterface;
use Spiral\Validator\RulesProvider;
use Spiral\Validator\Validation;

final class ValidatorBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        ValidationBootloader::class
    ];

    protected const SINGLETONS = [
        RulesInterface::class => RulesProvider::class,
    ];

    public function __construct(
        private readonly ConfiguratorInterface $config
    ) {
    }

    public function init(TokenizerBootloader $tokenizer): void
    {
        $this->initDefaultConfig($tokenizer);
    }

    public function boot(ValidationProvider $provider, ValidationBootloader $validation): void
    {
        $provider->register(
            FilterDefinition::class,
            static fn(Validation $validation): ValidationInterface => $validation
        );
        $validation->setDefaultValidator(FilterDefinition::class);
    }

    public function addChecker(string $alias, mixed $checker): void
    {
        $this->config->modify(ValidatorConfig::CONFIG, new Append('checkers', $alias, $checker));
    }

    public function addCondition(string $alias, mixed $condition): void
    {
        $this->config->modify(ValidatorConfig::CONFIG, new Append('conditions', $alias, $condition));
    }

    public function addAlias(string $alias, string $target): void
    {
        $this->config->modify(ValidatorConfig::CONFIG, new Append('aliases', $alias, $target));
    }

    private function initDefaultConfig(TokenizerBootloader $tokenizer): void
    {
        $this->config->setDefaults(
            ValidatorConfig::CONFIG,
            [
                // Checkers are resolved using container and provide ability to isolate some validation rules
                // under common name and class. You can register new checkers at any moment without any
                // performance issues.
                'checkers'   => [
                    'type'     => Checker\TypeChecker::class,
                    'number'   => Checker\NumberChecker::class,
                    'mixed'    => Checker\MixedChecker::class,
                    'address'  => Checker\AddressChecker::class,
                    'string'   => Checker\StringChecker::class,
                    'file'     => Checker\FileChecker::class,
                    'image'    => Checker\ImageChecker::class,
                    'datetime' => Checker\DatetimeChecker::class,
                    'array'    => Checker\ArrayChecker::class,
                    'boolean'  => Checker\BooleanChecker::class,
                ],

                // Enable/disable validation conditions
                'conditions' => [
                    'absent'     => Condition\AbsentCondition::class,
                    'present'    => Condition\PresentCondition::class,
                    'anyOf'      => Condition\AnyOfCondition::class,
                    'noneOf'     => Condition\NoneOfCondition::class,
                    'withAny'    => Condition\WithAnyCondition::class,
                    'withoutAny' => Condition\WithoutAnyCondition::class,
                    'withAll'    => Condition\WithAllCondition::class,
                    'withoutAll' => Condition\WithoutAllCondition::class,
                ],

                // Aliases are only used to simplify developer life.
                'aliases'    => [
                    'notEmpty'   => 'type::notEmpty',
                    'notNull'    => 'type::notNull',
                    'required'   => 'type::notEmpty',
                    'datetime'   => 'type::datetime',
                    'timezone'   => 'type::timezone',
                    'bool'       => 'type::boolean',
                    'boolean'    => 'type::boolean',
                    'arrayOf'    => 'array::of',
                    'cardNumber' => 'mixed::cardNumber',
                    'regexp'     => 'string::regexp',
                    'email'      => 'address::email',
                    'url'        => 'address::url',
                    'file'       => 'file::exists',
                    'uploaded'   => 'file::uploaded',
                    'filesize'   => 'file::size',
                    'image'      => 'image::valid',
                    'array'      => 'is_array',
                    'callable'   => 'is_callable',
                    'double'     => 'is_double',
                    'float'      => 'is_float',
                    'int'        => 'is_int',
                    'integer'    => 'is_integer',
                    'numeric'    => 'is_numeric',
                    'long'       => 'is_long',
                    'null'       => 'is_null',
                    'object'     => 'is_object',
                    'real'       => 'is_real',
                    'resource'   => 'is_resource',
                    'scalar'     => 'is_scalar',
                    'string'     => 'is_string',
                    'match'      => 'mixed::match',
                ],
            ]
        );

        $tokenizer->addDirectory(\dirname(
            (new \ReflectionClass(FilterDefinition::class))
                ->getFileName()
        ));
    }
}
