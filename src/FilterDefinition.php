<?php

declare(strict_types=1);

namespace Spiral\Validator;

use Spiral\Filters\FilterDefinitionInterface;
use Spiral\Filters\ShouldBeValidated;

class FilterDefinition implements FilterDefinitionInterface, ShouldBeValidated
{
    public function __construct(
        private array $validationRules = [],
        private array $mappingSchema = []
    ) {
    }

    public function mappingSchema(): array
    {
        return $this->mappingSchema;
    }

    public function validationRules(): array
    {
        return $this->validationRules;
    }
}
