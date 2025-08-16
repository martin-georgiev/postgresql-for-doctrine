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
            'simple jsonb array' => ['simple jsonb array', [['foo' => 'bar'], ['baz' => 123]]],
            'jsonb array with nested objects' => ['jsonb array with nested objects', [
                ['user' => ['id' => 1, 'name' => 'John']],
                ['user' => ['id' => 2, 'name' => 'Jane']],
            ]],
            'jsonb array with mixed types' => ['jsonb array with mixed types', [
                ['string' => 'value', 'number' => 42],
                ['boolean' => true, 'null' => null],
                ['array' => [1, 2, 3]],
            ]],
            'jsonb array with special characters' => ['jsonb array with special characters', [
                ['message' => 'Hello "World" with \'quotes\''],
                ['path' => '/path/with/slashes'],
            ]],
            'empty jsonb array' => ['empty jsonb array', []],
        ];
    }
}
