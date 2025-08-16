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

        $this->runTypeTest($typeName, $columnType, null);
    }

    #[Test]
    public function can_handle_empty_arrays(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runTypeTest($typeName, $columnType, []);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_json_values(string $testName, array $json): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runTypeTest($typeName, $columnType, $json);
    }

    /**
     * @return array<string, array{string, mixed}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple object' => ['simple object', ['foo' => 'bar', 'baz' => 123]],
            'nested structures' => ['nested structures', [
                'user' => ['id' => 1, 'name' => 'John'],
                'meta' => ['active' => true, 'roles' => ['admin', 'user']],
            ]],
            'mixed types' => ['mixed types', [
                'string' => 'value',
                'number' => 42,
                'boolean' => false,
                'null' => null,
                'array' => [1, 2, 3],
                'object' => ['a' => 1],
            ]],
            'special characters' => ['special characters', [
                'message' => 'Hello "World" with \'quotes\'',
                'path' => '/path/with/slashes',
            ]],
        ];
    }
}
