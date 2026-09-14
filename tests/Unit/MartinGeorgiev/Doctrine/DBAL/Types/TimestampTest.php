<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Timestamp;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class TimestampTest extends BaseDateTimeTestCase
{
    protected function createFixture(): Timestamp
    {
        return new Timestamp();
    }

    protected function getExpectedTypeName(): string
    {
        return 'timestamp';
    }

    protected static function getPHPExceptionClass(): string
    {
        return InvalidTimestampForPHPException::class;
    }

    protected static function getDatabaseExceptionClass(): string
    {
        return InvalidTimestampForDatabaseException::class;
    }

    protected static function getComparisonFormat(): string
    {
        return 'X-m-d H:i:s';
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
            'whole second' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15 10:30:45'),
                'postgresValue' => '2023-06-15 10:30:45.000000',
            ],
            'with microseconds' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15 10:30:45.123456'),
                'postgresValue' => '2023-06-15 10:30:45.123456',
            ],
            'midnight' => [
                'phpValue' => new \DateTimeImmutable('2000-01-01 00:00:00'),
                'postgresValue' => '2000-01-01 00:00:00.000000',
            ],
        ];
    }

    #[Test]
    public function converts_mutable_date_time_to_database_value(): void
    {
        $phpValue = self::createMutableDateTime('2023-06-15 10:30:45');

        $this->assertSame('2023-06-15 10:30:45.000000', $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[Test]
    public function converts_to_php_value_without_microseconds(): void
    {
        $result = $this->fixture->convertToPHPValue('2023-06-15 10:30:45', $this->platform);

        $this->assertInstanceOf(\DateTimeImmutable::class, $result);
        $this->assertSame('2023-06-15 10:30:45.000000', $result->format('Y-m-d H:i:s.u'));
    }

    /**
     * @return array<string, array{postgresValue: string, expectedFormattedValue: string}>
     */
    public static function provideBcEraAndExpandedYearTransformationsToPHP(): array
    {
        return [
            'first year of the BC era' => [
                'postgresValue' => '0001-01-15 10:30:00 BC',
                'expectedFormattedValue' => '+0000-01-15 10:30:00',
            ],
            'leap day of the first year of the BC era' => [
                'postgresValue' => '0001-02-29 10:30:00 BC',
                'expectedFormattedValue' => '+0000-02-29 10:30:00',
            ],
            'second year of the BC era' => [
                'postgresValue' => '0002-01-15 10:30:00 BC',
                'expectedFormattedValue' => '-0001-01-15 10:30:00',
            ],
            'five digit year' => [
                'postgresValue' => '10000-01-15 10:30:00',
                'expectedFormattedValue' => '+10000-01-15 10:30:00',
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
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s.u', '+0000-01-15 10:30:00.000000'),
                'expectedPostgresValue' => '0001-01-15 10:30:00.000000 BC',
            ],
            'negative astronomical year' => [
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s.u', '-0001-01-15 10:30:00.000000'),
                'expectedPostgresValue' => '0002-01-15 10:30:00.000000 BC',
            ],
            'five digit year' => [
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s.u', '+10000-01-15 10:30:00.000000'),
                'expectedPostgresValue' => '10000-01-15 10:30:00.000000',
            ],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueFormats(): array
    {
        return [
            'garbage string' => ['not-a-timestamp'],
            'date only' => ['2023-06-15'],
            'US date format' => ['06/15/2023 10:30:00'],
            'empty string' => [''],
            'era suffix without a value' => [' BC'],
            'era suffix on a garbage string' => ['not-a-timestamp BC'],
            'infinity spelled as a float' => ['inf'],
        ];
    }
}
