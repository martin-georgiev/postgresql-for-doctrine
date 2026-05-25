<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsMultirangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsMultirangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\TsMultirangeArray;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsMultirange as TsMultirangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TsMultirangeArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private TsMultirangeArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new TsMultirangeArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('tsmultirange[]', $this->fixture->getName());
    }

    #[Test]
    public function converts_null_to_database_value(): void
    {
        $this->assertNull($this->fixture->convertToDatabaseValue(null, $this->platform));
    }

    #[Test]
    public function converts_null_to_php_value(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue(null, $this->platform));
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
        $this->assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{
     *     phpValue: array<TsMultirangeValueObject|null>|null,
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
            'single multirange' => [
                'phpValue' => [new TsMultirangeValueObject([new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 17:00:00'))])],
                'postgresValue' => '{"{[2024-01-01 09:00:00.000000,2024-01-01 17:00:00.000000)}"}',
            ],
            'multirange with two ranges' => [
                'phpValue' => [new TsMultirangeValueObject([
                    new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 12:00:00')),
                    new TsRange(new \DateTimeImmutable('2024-01-01 14:00:00'), new \DateTimeImmutable('2024-01-01 17:00:00')),
                ])],
                'postgresValue' => '{"{[2024-01-01 09:00:00.000000,2024-01-01 12:00:00.000000),[2024-01-01 14:00:00.000000,2024-01-01 17:00:00.000000)}"}',
            ],
            'empty multirange item' => [
                'phpValue' => [new TsMultirangeValueObject([])],
                'postgresValue' => '{"{}"}',
            ],
            'array with null item' => [
                'phpValue' => [new TsMultirangeValueObject([new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 17:00:00'))]), null],
                'postgresValue' => '{"{[2024-01-01 09:00:00.000000,2024-01-01 17:00:00.000000)}",NULL}',
            ],
            'multiple multiranges' => [
                'phpValue' => [
                    new TsMultirangeValueObject([new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 17:00:00'))]),
                    new TsMultirangeValueObject([new TsRange(new \DateTimeImmutable('2024-01-02 10:00:00'), new \DateTimeImmutable('2024-01-02 18:00:00'))]),
                ],
                'postgresValue' => '{"{[2024-01-01 09:00:00.000000,2024-01-01 17:00:00.000000)}","{[2024-01-02 10:00:00.000000,2024-01-02 18:00:00.000000)}"}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidTsMultirangeArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'array containing string' => [['not-a-multirange']],
            'array containing integer' => [[42]],
            'array containing object' => [[new \stdClass()]],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidTsMultirangeArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputs(): array
    {
        return [
            'string instead of array' => ['not-an-array'],
            'integer instead of array' => [42],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(string $postgresValue): void
    {
        $this->expectException(InvalidTsMultirangeArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'invalid format in array' => ['{"not-a-multirange"}'],
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
            'empty multirange' => [new TsMultirangeValueObject([])],
            'single range multirange' => [new TsMultirangeValueObject([new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 17:00:00'))])],
            'null item' => [null],
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
            'string' => ['not-a-multirange'],
            'integer' => [42],
            'boolean' => [true],
            'object' => [new \stdClass()],
        ];
    }

    #[Test]
    public function can_transform_null_item_for_php(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    public function throws_exception_for_non_string_item_from_database(): void
    {
        $this->expectException(InvalidTsMultirangeArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(42);
    }
}
