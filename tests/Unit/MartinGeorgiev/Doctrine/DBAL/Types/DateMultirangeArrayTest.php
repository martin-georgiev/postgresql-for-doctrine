<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\DateMultirangeArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateMultirangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateMultirangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateMultirange as DateMultirangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DateMultirangeArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private DateMultirangeArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new DateMultirangeArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('datemultirange[]', $this->fixture->getName());
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
     *     phpValue: array<DateMultirangeValueObject|null>|null,
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
                'phpValue' => [new DateMultirangeValueObject([new DateRange(new \DateTimeImmutable('2024-01-01'), new \DateTimeImmutable('2024-06-30'))])],
                'postgresValue' => '{"{[2024-01-01,2024-06-30)}"}',
            ],
            'multirange with two ranges' => [
                'phpValue' => [new DateMultirangeValueObject([
                    new DateRange(new \DateTimeImmutable('2024-01-01'), new \DateTimeImmutable('2024-03-31')),
                    new DateRange(new \DateTimeImmutable('2024-07-01'), new \DateTimeImmutable('2024-12-31')),
                ])],
                'postgresValue' => '{"{[2024-01-01,2024-03-31),[2024-07-01,2024-12-31)}"}',
            ],
            'empty multirange item' => [
                'phpValue' => [new DateMultirangeValueObject([])],
                'postgresValue' => '{"{}"}',
            ],
            'multiple multiranges' => [
                'phpValue' => [
                    new DateMultirangeValueObject([new DateRange(new \DateTimeImmutable('2024-01-01'), new \DateTimeImmutable('2024-03-31'))]),
                    new DateMultirangeValueObject([new DateRange(new \DateTimeImmutable('2024-07-01'), new \DateTimeImmutable('2024-12-31'))]),
                ],
                'postgresValue' => '{"{[2024-01-01,2024-03-31)}","{[2024-07-01,2024-12-31)}"}',
            ],
            'array with null item' => [
                'phpValue' => [new DateMultirangeValueObject([new DateRange(new \DateTimeImmutable('2024-01-01'), new \DateTimeImmutable('2024-06-30'))]), null],
                'postgresValue' => '{"{[2024-01-01,2024-06-30)}",NULL}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidDateMultirangeArrayItemForDatabaseException::class);
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
        $this->expectException(InvalidDateMultirangeArrayItemForPHPException::class);
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
        $this->expectException(InvalidDateMultirangeArrayItemForPHPException::class);
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

    #[Test]
    public function throws_exception_for_non_string_item_from_database(): void
    {
        $this->expectException(InvalidDateMultirangeArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(42);
    }
}
