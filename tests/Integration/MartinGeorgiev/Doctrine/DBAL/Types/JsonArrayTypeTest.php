<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

final class JsonArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'json[]';
    }

    /**
     * @return array<string, array{array<int, array<string, mixed>>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple json array' => [[
                ['key1' => 'value1', 'key2' => false],
                ['key1' => 'value2', 'key2' => true],
            ]],
            'json array with nested structures' => [[
                [
                    'user' => ['id' => 1, 'name' => 'John'],
                    'meta' => ['active' => true, 'roles' => ['user']],
                ],
                [
                    'user' => ['id' => 2, 'name' => 'Jane'],
                    'meta' => ['active' => false, 'roles' => ['user']],
                ],
            ]],
            'json array with mixed types' => [[
                [
                    'string' => 'value',
                    'number' => 42,
                    'float' => 3.14,
                    'boolean' => false,
                    'null' => null,
                    'array' => [1, 2, 3],
                ],
                [
                    'different' => 'structure',
                    'count' => 999,
                    'enabled' => true,
                ],
            ]],
            'json array with big integers' => [[
                [
                    'bigint' => '9223372036854775807',
                    'regular' => 123,
                ],
                [
                    'huge_number' => '18446744073709551615',
                    'small' => 1,
                ],
            ]],
        ];
    }
}
