<?php

declare(strict_types=1);

namespace Spiral\Validator;

use Spiral\Filters\Model\FilterDefinitionInterface;
use Spiral\Filters\Model\ShouldBeValidated;

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
