<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidUuidArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidUuidArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\UuidArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UuidArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private UuidArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new UuidArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('uuid[]', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{
     *     phpValue: array|null,
     *     postgresValue: string|null
     * }>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            'empty array' => [
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
            'single UUID' => [
                'phpValue' => ['550e8400-e29b-41d4-a716-446655440000'],
                'postgresValue' => '{"550e8400-e29b-41d4-a716-446655440000"}',
            ],
            'multiple UUIDs' => [
                'phpValue' => [
                    '550e8400-e29b-41d4-a716-446655440000',
                    'a0eebc99-9c0b-11d1-b465-00c04fd430c8',
                ],
                'postgresValue' => '{"550e8400-e29b-41d4-a716-446655440000","a0eebc99-9c0b-11d1-b465-00c04fd430c8"}',
            ],
            'uppercase UUIDs' => [
                'phpValue' => ['550E8400-E29B-41D4-A716-446655440000'],
                'postgresValue' => '{"550E8400-E29B-41D4-A716-446655440000"}',
            ],
            'UUID v1' => [
                'phpValue' => ['a0eebc99-9c0b-11d1-b465-00c04fd430c8'],
                'postgresValue' => '{"a0eebc99-9c0b-11d1-b465-00c04fd430c8"}',
            ],
            'UUID v4' => [
                'phpValue' => ['550e8400-e29b-41d4-a716-446655440000'],
                'postgresValue' => '{"550e8400-e29b-41d4-a716-446655440000"}',
            ],
            'UUID v7' => [
                'phpValue' => ['018e7e39-9f42-7000-8000-000000000000'],
                'postgresValue' => '{"018e7e39-9f42-7000-8000-000000000000"}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidUuidArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'invalid UUID format' => [['not-a-uuid']],
            'too short' => [['550e8400-e29b-41d4-a716']],
            'too long' => [['550e8400-e29b-41d4-a716-446655440000-extra']],
            'no hyphens' => [['550e8400e29b41d4a716446655440000']],
            'wrong separator' => [['550e8400_e29b_41d4_a716_446655440000']],
            'non-hex characters' => [['550e8400-e29b-41d4-a716-44665544ZZZZ']],
            'mixed valid and invalid' => [['550e8400-e29b-41d4-a716-446655440000', 'invalid-uuid']],
            'empty string' => [['']],
            'whitespace only' => [[' ']],
            'non-string item' => [[123]],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(string $postgresValue): void
    {
        $this->expectException(InvalidUuidArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'invalid format' => ['{"invalid-uuid"}'],
            'invalid UUID in array' => ['{"550e8400-e29b-41d4-a716-44665544ZZZZ"}'],
            'malformed array' => ['not-an-array'],
            'empty item in array' => ['{"550e8400-e29b-41d4-a716-446655440000",""}'],
            'invalid item in array' => ['{"550e8400-e29b-41d4-a716-446655440000","invalid-uuid"}'],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidUuidArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputs(): array
    {
        return [
            'string instead of array' => ['not-an-array'],
        ];
    }

    #[DataProvider('provideValidArrayItemsForDatabase')]
    #[Test]
    public function can_validate_valid_array_item_for_database(mixed $value): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideValidArrayItemsForDatabase(): array
    {
        return [
            'lowercase UUID' => ['550e8400-e29b-41d4-a716-446655440000'],
            'uppercase UUID' => ['550E8400-E29B-41D4-A716-446655440000'],
            'mixed case UUID' => ['A0eeBc99-9C0b-11D1-B465-00c04FD430c8'],
            'null value' => [null],
        ];
    }

    #[DataProvider('provideInvalidArrayItemsForDatabase')]
    #[Test]
    public function can_validate_invalid_array_item_for_database(mixed $value): void
    {
        $this->assertFalse($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidArrayItemsForDatabase(): array
    {
        return [
            'invalid UUID' => ['invalid-uuid'],
            'integer' => [123],
            'empty string' => [''],
            'boolean' => [true],
            'too short' => ['550e8400-e29b-41d4-a716'],
            'no hyphens' => ['550e8400e29b41d4a716446655440000'],
        ];
    }

    #[Test]
    public function can_convert_array_with_null_to_database(): void
    {
        $phpValue = ['550e8400-e29b-41d4-a716-446655440000', null, 'a0eebc99-9c0b-11d1-b465-00c04fd430c8'];
        $expected = '{"550e8400-e29b-41d4-a716-446655440000",NULL,"a0eebc99-9c0b-11d1-b465-00c04fd430c8"}';

        $this->assertSame($expected, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[Test]
    public function can_transform_null_item_for_php(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    public function throws_exception_for_non_string_item_from_database(): void
    {
        $this->expectException(InvalidUuidArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(123);
    }
}
