<?php

declare(strict_types=1);

namespace Spiral\Validator\Condition;

use Spiral\Validator\ConditionInterface;
use Spiral\Validator\RulesInterface;

/**
 * @internal
 */
final class Compositor
{
    public function __construct(
        private readonly RulesInterface $provider
    ) {
    }

    /**
     * @return iterable<ConditionInterface>
     */
    public function makeConditions(string $field, array $options): iterable
    {
        $rules = $this->provider->getRules([
            $field => [
                static function (): void {
                },
                'if' => $options,
            ],
        ]);

        foreach ($rules as $rule) {
            return $rule->getConditions();
        }

        return [];
    }
}
