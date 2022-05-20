<?php

declare(strict_types=1);

namespace Spiral\Validator;

abstract class AbstractCondition implements ConditionInterface
{
    protected array $options = [];

    public function withOptions(?array $options): ConditionInterface
    {
        $condition = clone $this;
        $condition->options = $options ?? [];

        return $condition;
    }
}
