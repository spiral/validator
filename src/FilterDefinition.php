<?php

declare(strict_types=1);

namespace Spiral\Validator;

use Spiral\Filters\Dto\FilterDefinitionInterface;
use Spiral\Filters\Dto\ShouldBeValidated;

class FilterDefinition implements FilterDefinitionInterface, ShouldBeValidated
{
    public function __construct(
        private readonly array $validationRules = [],
        private readonly array $mappingSchema = []
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
