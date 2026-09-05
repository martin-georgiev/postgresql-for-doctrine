<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidUlidArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidUlidArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\UlidArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class UlidArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&Stub
     */
    private Stub $platform;

    private UlidArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createStub(AbstractPlatform::class);
        $this->fixture = new UlidArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('ulid[]', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_database_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(?array $phpValue, ?string $postgresValue): void
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
            'single ULID' => [
                'phpValue' => ['01ARZ3NDEKTSV4RRFFQ69G5FAV'],
                'postgresValue' => '{"01ARZ3NDEKTSV4RRFFQ69G5FAV"}',
            ],
            'multiple ULIDs' => [
                'phpValue' => [
                    '01ARZ3NDEKTSV4RRFFQ69G5FAV',
                    '01BX5ZZKBKACTAV9WEVGEMMVRZ',
                ],
                'postgresValue' => '{"01ARZ3NDEKTSV4RRFFQ69G5FAV","01BX5ZZKBKACTAV9WEVGEMMVRZ"}',
            ],
            'minimum ULID' => [
                'phpValue' => ['00000000000000000000000000'],
                'postgresValue' => '{"00000000000000000000000000"}',
            ],
            'maximum ULID' => [
                'phpValue' => ['7ZZZZZZZZZZZZZZZZZZZZZZZZZ'],
                'postgresValue' => '{"7ZZZZZZZZZZZZZZZZZZZZZZZZZ"}',
            ],
        ];
    }

    #[Test]
    public function normalizes_case_of_items_for_database_value(): void
    {
        $phpValue = ['01arz3ndektsv4rrffq69g5fav'];

        $this->assertSame('{"01ARZ3NDEKTSV4RRFFQ69G5FAV"}', $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[Test]
    public function normalizes_case_of_items_for_php_value(): void
    {
        $this->assertSame(['01ARZ3NDEKTSV4RRFFQ69G5FAV'], $this->fixture->convertToPHPValue('{"01arz3ndektsv4rrffq69g5fav"}', $this->platform));
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidUlidArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'invalid ULID format' => [['not-a-ulid']],
            'too short' => [['01ARZ3NDEKTSV4RRFFQ69G5FA']],
            'too long' => [['01ARZ3NDEKTSV4RRFFQ69G5FAV0']],
            'first character above 7' => [['81ARZ3NDEKTSV4RRFFQ69G5FAV']],
            'excluded letters' => [['0ILOU3NDEKTSV4RRFFQ69G5FAV']],
            'mixed valid and invalid' => [['01ARZ3NDEKTSV4RRFFQ69G5FAV', 'invalid-ulid']],
            'empty string' => [['']],
            'whitespace only' => [[' ']],
            'non-string item' => [[123]],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(string $postgresValue): void
    {
        $this->expectException(InvalidUlidArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'invalid format' => ['{"invalid-ulid"}'],
            'ULID with excluded letters' => ['{"0ILOU3NDEKTSV4RRFFQ69G5FAV"}'],
            'malformed array' => ['not-an-array'],
            'empty item in array' => ['{"01ARZ3NDEKTSV4RRFFQ69G5FAV",""}'],
            'invalid item in array' => ['{"01ARZ3NDEKTSV4RRFFQ69G5FAV","invalid-ulid"}'],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidUlidArrayItemForPHPException::class);
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
    public function validates_valid_array_item_for_database(mixed $value): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideValidArrayItemsForDatabase(): array
    {
        return [
            'uppercase ULID' => ['01ARZ3NDEKTSV4RRFFQ69G5FAV'],
            'lowercase ULID' => ['01arz3ndektsv4rrffq69g5fav'],
            'mixed case ULID' => ['01aRz3NdEkTsV4rRfFq69g5FaV'],
            'null value' => [null],
        ];
    }

    #[DataProvider('provideInvalidArrayItemsForDatabase')]
    #[Test]
    public function validates_invalid_array_item_for_database(mixed $value): void
    {
        $this->assertFalse($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidArrayItemsForDatabase(): array
    {
        return [
            'invalid ULID' => ['invalid-ulid'],
            'integer' => [123],
            'empty string' => [''],
            'boolean' => [true],
            'too short' => ['01ARZ3NDEKTSV4RRFFQ69G5FA'],
            'first character above 7' => ['81ARZ3NDEKTSV4RRFFQ69G5FAV'],
        ];
    }

    #[Test]
    public function converts_unquoted_database_value_to_php_value(): void
    {
        // PostgreSQL outputs ULID array items without quotes (e.g. {01ARZ...,NULL});
        // all-digit ULIDs must stay strings and the NULL literal must become null
        $expected = ['00000000000000000000000000', null, '01ARZ3NDEKTSV4RRFFQ69G5FAV'];

        $this->assertSame($expected, $this->fixture->convertToPHPValue('{00000000000000000000000000,NULL,01ARZ3NDEKTSV4RRFFQ69G5FAV}', $this->platform));
    }

    #[Test]
    public function converts_array_with_null_to_database_value(): void
    {
        $phpValue = ['01ARZ3NDEKTSV4RRFFQ69G5FAV', null, '01BX5ZZKBKACTAV9WEVGEMMVRZ'];
        $expected = '{"01ARZ3NDEKTSV4RRFFQ69G5FAV",NULL,"01BX5ZZKBKACTAV9WEVGEMMVRZ"}';

        $this->assertSame($expected, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[Test]
    public function converts_null_item_to_php_value(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    public function throws_exception_for_non_string_item_from_database(): void
    {
        $this->expectException(InvalidUlidArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(123);
    }
}
