<?php

declare(strict_types=1);

namespace Spiral\Validator;

use Spiral\Filters\Filter;
use Spiral\Validation\ValidationInterface;
use Spiral\Validation\ValidatorInterface;
use Spiral\Filters\FilterBag;

class Validation implements ValidationInterface
{
    public function __construct(
       protected RulesInterface $rules
    ) {
    }

    public function validate(mixed $data, array $rules, $context = null): ValidatorInterface
    {
        if ($data instanceof FilterBag) {
            $data = $data->filter;
        }

        if ($data instanceof Filter) {
            $data = $data->getData();
        }

        return new Validator($data, $rules, $context, $this->rules);
    }
}
