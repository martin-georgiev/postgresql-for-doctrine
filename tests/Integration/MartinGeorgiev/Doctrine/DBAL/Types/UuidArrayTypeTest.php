<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidUuidArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class UuidArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'uuid[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'UUID[]';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_array_values(string $testName, array $arrayValue): void
    {
        parent::can_handle_array_values($testName, $arrayValue);
    }

    /**
     * @return array<string, array{string, array<int, string>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single UUID' => ['single UUID', ['550e8400-e29b-41d4-a716-446655440000']],
            'multiple UUIDs' => ['multiple UUIDs', [
                '550e8400-e29b-41d4-a716-446655440000',
                'a0eebc99-9c0b-11d1-b465-00c04fd430c8',
                '018e7e39-9f42-7000-8000-000000000000',
            ]],
            'UUID v1' => ['UUID v1', ['a0eebc99-9c0b-11d1-b465-00c04fd430c8']],
            'UUID v4' => ['UUID v4', ['550e8400-e29b-41d4-a716-446655440000']],
            'UUID v7' => ['UUID v7', ['018e7e39-9f42-7000-8000-000000000000']],
            'empty UUID array' => ['empty UUID array', []],
        ];
    }

    #[Test]
    public function can_handle_invalid_uuids(): void
    {
        $this->expectException(InvalidUuidArrayItemForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, ['invalid-uuid', '550e8400-e29b-41d4-a716-446655440000']);
    }
}
