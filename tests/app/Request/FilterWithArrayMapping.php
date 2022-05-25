<?php

declare(strict_types=1);

namespace Spiral\Validator\App\Request;

use Spiral\Filters\Filter;
use Spiral\Filters\FilterDefinitionInterface;
use Spiral\Filters\HasFilterDefinition;
use Spiral\Validator\FilterDefinition;

class FilterWithArrayMapping extends Filter implements HasFilterDefinition
{
    public function filterDefinition(): FilterDefinitionInterface
    {
        return new FilterDefinition(
            [
                'username' => ['string', 'required'],
                'email' => ['email', 'required']
            ],
            [
                'username' => 'username',
                'email' => 'email'
            ]
        );
    }
}
