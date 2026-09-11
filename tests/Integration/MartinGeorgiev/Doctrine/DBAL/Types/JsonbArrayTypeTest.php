<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class JsonbArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'jsonb[]';
    }

    /**
     * @param array<int, mixed> $arrayValue
     */
    #[DataProvider('provideValidTransformations')]
    #[DataProvider('provideTypeInferenceTestCases')]
    #[DataProvider('provideScalarItemTestCases')]
    #[Test]
    public function roundtrips_value(array $arrayValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $arrayValue);
    }

    /**
     * @return array<string, array{array<int, array<string, mixed>>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple jsonb array' => [[
                ['key1' => 'value1', 'key2' => false],
                ['key1' => 'value2', 'key2' => true],
            ]],
            'jsonb array with nested structures' => [[
                [
                    'user' => ['id' => 1, 'name' => 'John'],
                    'meta' => ['active' => true, 'roles' => ['user']],
                ],
                [
                    'user' => ['id' => 2, 'name' => 'Jane'],
                    'meta' => ['active' => false, 'roles' => ['user']],
                ],
            ]],
            'jsonb array with mixed types' => [[
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
            'jsonb array with big integers' => [[
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
     * @return array<string, array{array<int, mixed>}>
     */
    public static function provideScalarItemTestCases(): array
    {
        return [
            'jsonb array of numbers' => [[1, -2, 3.5]],
            'jsonb array of booleans' => [[true, false]],
            'jsonb array of strings' => [['hello', 'null', '1']],
            'jsonb array of booleans beside their string lookalikes' => [[true, false, 'true', 'false', 't', 'f']],
            'jsonb array of json nulls' => [[null, null]],
            'jsonb array mixing scalars and objects' => [[1, 'two', true, null, ['key' => 'value'], [1, 2]]],
        ];
    }

    /**
     * @return array<string, array{array<int, array<string, mixed>>}>
     */
    public static function provideTypeInferenceTestCases(): array
    {
        return [
            'numeric types preserved' => [[
                [
                    'integer' => 42,
                    'float' => 3.14,
                    'zero' => 0,
                    'negative' => -123,
                ],
            ]],
            'decimal numbers as floats' => [[
                [
                    'price' => 502.00,
                    'tax' => 505.50,
                    'discount' => 0.99,
                ],
            ]],
            'boolean and null types' => [[
                [
                    'active' => true,
                    'deleted' => false,
                    'metadata' => null,
                ],
            ]],
            'mixed numeric and string types' => [[
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
