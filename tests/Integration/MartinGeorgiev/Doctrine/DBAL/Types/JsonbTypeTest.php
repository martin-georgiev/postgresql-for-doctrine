<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class JsonbTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'jsonb';
    }

    protected function getPostgresTypeName(): string
    {
        return 'JSONB';
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, null);
    }

    #[Test]
    public function can_handle_empty_arrays(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, []);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_json_values(array $json): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $json);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple object' => [['foo' => 'bar', 'baz' => 123]],
            'nested structures' => [[
                'user' => ['id' => 1, 'name' => 'John'],
                'meta' => ['active' => true, 'roles' => ['user']],
            ]],
            'mixed types' => [[
                'string' => 'value',
                'number' => 42,
                'boolean' => false,
                'null' => null,
                'array' => [1, 2, 3],
                'object' => ['a' => 1],
            ]],
            'special characters' => [[
                'message' => 'Hello "World" with \'quotes\'',
                'path' => '/path/with/slashes',
            ]],
        ];
    }

    #[DataProvider('provideScalarTransformations')]
    #[Test]
    public function can_handle_scalar_json_values(mixed $value): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $value);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideScalarTransformations(): array
    {
        return [
            'integer' => [42],
            'float' => [3.14],
            'boolean true' => [true],
            'boolean false' => [false],
            'string' => ['hello world'],
        ];
    }
}
