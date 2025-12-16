<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class JsonbArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'jsonb[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'JSONB[]';
    }

    #[DataProvider('provideValidTransformations')]
    #[DataProvider('provideTypeInferenceTestCases')]
    #[Test]
    public function can_handle_array_values(string $testName, array $arrayValue): void
    {
        parent::can_handle_array_values($testName, $arrayValue);
    }

    /**
     * @return array<string, array{string, array<int, array<string, mixed>>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple jsonb array' => ['simple jsonb array', [
                ['key1' => 'value1', 'key2' => false],
                ['key1' => 'value2', 'key2' => true],
            ]],
            'jsonb array with nested structures' => ['jsonb array with nested structures', [
                [
                    'user' => ['id' => 1, 'name' => 'John'],
                    'meta' => ['active' => true, 'roles' => ['admin', 'user']],
                ],
                [
                    'user' => ['id' => 2, 'name' => 'Jane'],
                    'meta' => ['active' => false, 'roles' => ['user']],
                ],
            ]],
            'jsonb array with mixed types' => ['jsonb array with mixed types', [
                [
                    'string' => 'value',
                    'number' => 42,
                    'boolean' => false,
                    'null' => null,
                    'array' => [1, 2, 3],
                    'object' => ['a' => 1],
                ],
                [
                    'different' => 'structure',
                    'count' => 999,
                    'enabled' => true,
                ],
            ]],
            'jsonb array with big integers' => ['jsonb array with big integers', [
                [
                    'bigint' => '9223372036854775807', // PHP_INT_MAX as string
                    'regular' => 123,
                ],
                [
                    'huge_number' => '18446744073709551615', // Larger than PHP_INT_MAX
                    'small' => 1,
                ],
            ]],
        ];
    }

    /**
     * Verify that JsonbArray performs type inference correctly (default behavior) as
     * JSON values should maintain their proper types (integers, floats, booleans, null).
     */
    public static function provideTypeInferenceTestCases(): array
    {
        return [
            'numeric types preserved' => ['numeric types should be preserved correctly', [
                [
                    'integer' => 42,
                    'float' => 3.14,
                    'zero' => 0,
                    'negative' => -123,
                ],
            ]],
            'decimal numbers as floats' => ['decimal numbers should be floats', [
                [
                    'price' => 502.00,
                    'tax' => 505.50,
                    'discount' => 0.99,
                ],
            ]],
            'boolean and null types' => ['boolean and null types should be preserved', [
                [
                    'active' => true,
                    'deleted' => false,
                    'metadata' => null,
                ],
            ]],
            'mixed numeric and string types' => ['mixed types should maintain their types', [
                [
                    'id' => 123,
                    'name' => 'Product',
                    'price' => 99.99,
                    'available' => true,
                    'description' => null,
                ],
            ]],
        ];
    }
}
