<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Date;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class DateTest extends BaseDateTimeTestCase
{
    protected function createFixture(): Date
    {
        return new Date();
    }

    protected function getExpectedTypeName(): string
    {
        return 'date';
    }

    protected static function getPHPExceptionClass(): string
    {
        return InvalidDateForPHPException::class;
    }

    protected static function getDatabaseExceptionClass(): string
    {
        return InvalidDateForDatabaseException::class;
    }

    protected static function getComparisonFormat(): string
    {
        return 'X-m-d';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_database_value(\DateTimeImmutable $phpValue, string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(\DateTimeImmutable $phpValue, string $postgresValue): void
    {
        $this->assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{phpValue: \DateTimeImmutable, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'standard date' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15'),
                'postgresValue' => '2023-06-15',
            ],
            'leap day' => [
                'phpValue' => new \DateTimeImmutable('2024-02-29'),
                'postgresValue' => '2024-02-29',
            ],
            'first day of the year' => [
                'phpValue' => new \DateTimeImmutable('2000-01-01'),
                'postgresValue' => '2000-01-01',
            ],
        ];
    }

    #[Test]
    public function converts_mutable_date_time_to_database_value(): void
    {
        $phpValue = self::createMutableDateTime('2023-06-15');

        $this->assertSame('2023-06-15', $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[Test]
    public function converts_to_php_value_at_midnight(): void
    {
        $result = $this->fixture->convertToPHPValue('2023-06-15', $this->platform);

        $this->assertInstanceOf(\DateTimeImmutable::class, $result);
        $this->assertSame('2023-06-15 00:00:00.000000', $result->format('Y-m-d H:i:s.u'));
    }

    /**
     * @return array<string, array{postgresValue: string, expectedFormattedValue: string}>
     */
    public static function provideBcEraAndExpandedYearTransformationsToPHP(): array
    {
        return [
            'first year of the BC era' => [
                'postgresValue' => '0001-01-15 BC',
                'expectedFormattedValue' => '+0000-01-15',
            ],
            'leap day of the first year of the BC era' => [
                'postgresValue' => '0001-02-29 BC',
                'expectedFormattedValue' => '+0000-02-29',
            ],
            'second year of the BC era' => [
                'postgresValue' => '0002-01-15 BC',
                'expectedFormattedValue' => '-0001-01-15',
            ],
            'five digit year' => [
                'postgresValue' => '10000-01-15',
                'expectedFormattedValue' => '+10000-01-15',
            ],
            'first year of the AD era' => [
                'postgresValue' => '0001-01-01',
                'expectedFormattedValue' => '+0001-01-01',
            ],
        ];
    }

    /**
     * @return array<string, array{phpValue: \DateTimeImmutable, expectedPostgresValue: string}>
     */
    public static function provideBcEraAndExpandedYearTransformationsToPostgres(): array
    {
        return [
            'astronomical year zero' => [
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s', '+0000-01-15 00:00:00'),
                'expectedPostgresValue' => '0001-01-15 BC',
            ],
            'leap day of astronomical year zero' => [
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s', '+0000-02-29 00:00:00'),
                'expectedPostgresValue' => '0001-02-29 BC',
            ],
            'negative astronomical year' => [
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s', '-0001-01-15 00:00:00'),
                'expectedPostgresValue' => '0002-01-15 BC',
            ],
            'five digit year' => [
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s', '+10000-01-15 00:00:00'),
                'expectedPostgresValue' => '10000-01-15',
            ],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueFormats(): array
    {
        return [
            'garbage string' => ['not-a-date'],
            'date with time' => ['2023-06-15 10:30:00'],
            'US date format' => ['06/15/2023'],
            'empty string' => [''],
            'era suffix without a value' => [' BC'],
            'era suffix on a garbage string' => ['not-a-date BC'],
            'infinity spelled as a float' => ['inf'],
        ];
    }
}
