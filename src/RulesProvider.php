<?php

declare(strict_types=1);

namespace Spiral\Validator;

use Spiral\Core\Container;
use Spiral\Core\Container\Autowire;
use Spiral\Core\Exception\Container\ContainerException;
use Spiral\Core\FactoryInterface;
use Spiral\Validator\Config\ValidatorConfig;

final class RulesProvider implements RulesInterface
{
    public function __construct(
        private readonly ValidatorConfig $config,
        private readonly ParserInterface $parser = new RuleParser(),
        private readonly FactoryInterface $factory = new Container()
    ) {
    }

    /** @var RuleInterface[] */
    private array $rules = [];

    /**
     * @inheritdoc
     *
     * Attention, for performance reasons method would cache all defined rules.
     */
    public function getRules(mixed $rules): \Generator
    {
        foreach ($this->parser->split($rules) as $id => $rule) {
            if (empty($id) || $rule instanceof \Closure) {
                yield new CallableRule($rule);
                continue;
            }

            // fetch from cache
            if (isset($this->rules[$id])) {
                yield $this->rules[$id];
                continue;
            }

            $function = $this->parser->parseCheck($rule);
            $conditions = $this->parser->parseConditions($rule);

            $check = $this->makeRule(
                $this->config->mapFunction($function),
                $rule
            );

            yield $this->rules[$id] = $check->withConditions($this->makeConditions($conditions));
        }
    }

    /**
     * Reset rules cache.
     *
     * @codeCoverageIgnore
     */
    public function resetCache(): void
    {
        $this->rules = [];
    }

    /**
     * Construct rule object.
     *
     * @throws ContainerException
     */
    private function makeRule(mixed $check, mixed $rule): RuleInterface
    {
        $args = $this->parser->parseArgs($rule);
        $message = $this->parser->parseMessage($rule);

        if (!\is_array($check)) {
            return new CallableRule($check, $args, $message);
        }

        if (\is_string($check[0]) && $this->config->hasChecker($check[0])) {
            /** @var CheckerInterface $checker */
            $checker = $this->config->getChecker($check[0])->resolve($this->factory);

            return new CheckerRule($checker, $check[1], $args, $message);
        }

        if (!\is_object($check[0])) {
            $check[0] = (new Autowire($check[0]))->resolve($this->factory);
        }

        return new CallableRule($check, $args, $message);
    }

    /**
     * @throws ContainerException
     */
    private function makeConditions(array $conditions): ?\SplObjectStorage
    {
        if (empty($conditions)) {
            return null;
        }

        $storage = new \SplObjectStorage();
        foreach ($conditions as $condition => $options) {
            $condition = $this->config->resolveAlias($condition);

            if ($this->config->hasCondition($condition)) {
                $autowire = $this->config->getCondition($condition);
            } else {
                $autowire = new Autowire($condition);
            }

            /** @psalm-suppress InvalidArgument */
            $storage->attach($autowire->resolve($this->factory), $options);
        }

        return $storage;
    }
}
