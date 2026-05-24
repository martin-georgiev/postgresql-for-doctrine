<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTstzMultirangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTstzMultirangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\TstzMultirangeArray;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzMultirange as TstzMultirangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TstzMultirangeArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private TstzMultirangeArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new TstzMultirangeArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('tstzmultirange[]', $this->fixture->getName());
    }

    #[Test]
    public function converts_null_to_null(): void
    {
        $this->assertNull($this->fixture->convertToDatabaseValue(null, $this->platform));
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
     *     phpValue: array<TstzMultirangeValueObject|null>|null,
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
                'phpValue' => [new TstzMultirangeValueObject([new TstzRange(new \DateTimeImmutable('2024-01-01 09:00:00+00:00'), new \DateTimeImmutable('2024-01-01 17:00:00+00:00'))])],
                'postgresValue' => '{"{[2024-01-01 09:00:00.000000+00:00,2024-01-01 17:00:00.000000+00:00)}"}',
            ],
            'multirange with two ranges' => [
                'phpValue' => [new TstzMultirangeValueObject([
                    new TstzRange(new \DateTimeImmutable('2024-01-01 09:00:00+00:00'), new \DateTimeImmutable('2024-01-01 12:00:00+00:00')),
                    new TstzRange(new \DateTimeImmutable('2024-01-01 14:00:00+00:00'), new \DateTimeImmutable('2024-01-01 17:00:00+00:00')),
                ])],
                'postgresValue' => '{"{[2024-01-01 09:00:00.000000+00:00,2024-01-01 12:00:00.000000+00:00),[2024-01-01 14:00:00.000000+00:00,2024-01-01 17:00:00.000000+00:00)}"}',
            ],
            'empty multirange item' => [
                'phpValue' => [new TstzMultirangeValueObject([])],
                'postgresValue' => '{"{}"}',
            ],
            'array with null item' => [
                'phpValue' => [new TstzMultirangeValueObject([new TstzRange(new \DateTimeImmutable('2024-01-01 09:00:00+00:00'), new \DateTimeImmutable('2024-01-01 17:00:00+00:00'))]), null],
                'postgresValue' => '{"{[2024-01-01 09:00:00.000000+00:00,2024-01-01 17:00:00.000000+00:00)}",NULL}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidTstzMultirangeArrayItemForDatabaseException::class);
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
        $this->expectException(InvalidTstzMultirangeArrayItemForPHPException::class);
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
        $this->expectException(InvalidTstzMultirangeArrayItemForPHPException::class);
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
            'empty multirange' => [new TstzMultirangeValueObject([])],
            'single range multirange' => [new TstzMultirangeValueObject([new TstzRange(new \DateTimeImmutable('2024-01-01 09:00:00+00:00'), new \DateTimeImmutable('2024-01-01 17:00:00+00:00'))])],
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
        $this->expectException(InvalidTstzMultirangeArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(42);
    }
}
