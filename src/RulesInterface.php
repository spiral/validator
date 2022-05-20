<?php

declare(strict_types=1);

namespace Spiral\Validator;

use Spiral\Validation\Exception\ValidationException;

/**
 * Responsible for providing validation rules based on given schema.
 */
interface RulesInterface
{
    /**
     * Parse rule definition into array of rules.
     *
     * @psalm-return \Generator<RuleInterface>
     *
     * @throws ValidationException
     */
    public function getRules(mixed $rules): \Generator;
}
