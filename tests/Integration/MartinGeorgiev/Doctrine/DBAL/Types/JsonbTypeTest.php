<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class JsonbTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'jsonb';
    }

    #[Test]
    public function roundtrips_null_value(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, null);
    }

    #[Test]
    public function roundtrips_empty_array(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, []);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(array $json): void
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
    public function roundtrips_scalar_value(mixed $value): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $value);
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
