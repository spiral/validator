<?php

declare(strict_types=1);

namespace Spiral\Validator\Tests\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use Spiral\Filters\Exception\ValidationException;
use Spiral\Filters\InputInterface;
use Spiral\Validator\App\Request\FilterWithArrayMapping;
use Spiral\Validator\App\Request\SimpleFilter;

final class ValidationTest extends BaseTestCase
{
    #[DataProvider('requestsSuccessProvider')]
    public function testValidationSuccess(string $filterClass, array $data): void
    {
        $this->getContainer()->bind(InputInterface::class, $this->initInputScope($data));

        $filter = $this->getContainer()->get($filterClass);

        $this->assertSame($data, $filter->getData());
    }

    #[DataProvider('requestsErrorProvider')]
    public function testValidationError(string $filterClass, array $data): void
    {
        $this->getContainer()->bind(InputInterface::class, $this->initInputScope($data));

        $this->expectException(ValidationException::class);
        $this->getContainer()->get($filterClass);
    }

    public static function requestsSuccessProvider(): \Traversable
    {
        yield [SimpleFilter::class, ['username' => 'foo', 'email' => 'foo@gmail.com']];
        yield [FilterWithArrayMapping::class, ['username' => 'foo', 'email' => 'foo@gmail.com']];
    }

    public static function requestsErrorProvider(): \Traversable
    {
        yield [SimpleFilter::class, ['email' => 'foo@gmail.com']];
        yield [SimpleFilter::class, ['username' => 'foo']];
        yield [SimpleFilter::class, ['username' => 'foo', 'email' => 'foo']];

        yield [FilterWithArrayMapping::class, ['email' => 'foo@gmail.com']];
        yield [FilterWithArrayMapping::class, ['username' => 'foo']];
        yield [FilterWithArrayMapping::class, ['username' => 'foo', 'email' => 'foo']];
    }

    private function initInputScope(array $data): InputInterface
    {
        return new class($data) implements InputInterface {

            public function __construct(
                private array $data
            ) {
            }

            public function withPrefix(string $prefix, bool $add = true): InputInterface
            {
                return $this;
            }

            public function getValue(string $source, string $name = null): mixed
            {
                return $this->data[$name] ?? null;
            }

            public function hasValue(string $source, string $name): bool
            {
                return isset($this->data);
            }
        };
    }
}
