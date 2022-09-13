<?php

declare(strict_types=1);

namespace Spiral\Validator\App;

use Spiral\Filters\Exception\InputException;
use Spiral\Filters\InputInterface;
use Spiral\Http\Request\InputManager;

final class InputScope implements InputInterface
{
    public function __construct(
        private InputManager $input
    ) {
    }

    public function withPrefix(string $prefix, bool $add = true): InputInterface
    {
        $input = clone $this;
        $input->input = $this->input->withPrefix($prefix, $add);

        return $input;
    }

    public function getValue(string $source, mixed $name = null): mixed
    {
        if (!$this->input->hasBag($source)) {
            throw new InputException(\sprintf('Undefined input source %s', $source));
        }

        return \call_user_func([$this->input, $source], $name);
    }

    public function hasValue(string $source, string $name): bool
    {
        if (!$this->input->hasBag($source)) {
            return false;
        }

        return $this->input->bag($source)->has($name);
    }
}
