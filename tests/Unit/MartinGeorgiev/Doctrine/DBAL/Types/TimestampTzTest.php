<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\TimestampTz;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class TimestampTzTest extends BaseDateTimeTestCase
{
    protected function createFixture(): TimestampTz
    {
        return new TimestampTz();
    }

    protected function getExpectedTypeName(): string
    {
        return 'timestamptz';
    }

    protected static function getPHPExceptionClass(): string
    {
        return InvalidTimestampTzForPHPException::class;
    }

    protected static function getDatabaseExceptionClass(): string
    {
        return InvalidTimestampTzForDatabaseException::class;
    }

    protected static function getComparisonFormat(): string
    {
        return 'X-m-d H:i:sP';
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
            'UTC offset' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15 10:30:45+00:00'),
                'postgresValue' => '2023-06-15 10:30:45.000000+00:00',
            ],
            'positive offset' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15 10:30:45+02:00'),
                'postgresValue' => '2023-06-15 10:30:45.000000+02:00',
            ],
            'negative offset' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15 10:30:45-05:00'),
                'postgresValue' => '2023-06-15 10:30:45.000000-05:00',
            ],
            'with microseconds' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15 10:30:45.123456+02:00'),
                'postgresValue' => '2023-06-15 10:30:45.123456+02:00',
            ],
        ];
    }

    #[Test]
    public function converts_mutable_date_time_to_database_value(): void
    {
        $phpValue = self::createMutableDateTime('2023-06-15 10:30:45+02:00');

        $this->assertSame('2023-06-15 10:30:45.000000+02:00', $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideOffsetPreservations')]
    #[Test]
    public function preserves_offset_on_php_value(string $postgresValue, string $expectedDatetime, string $expectedOffset): void
    {
        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);

        $this->assertInstanceOf(\DateTimeImmutable::class, $result);
        $this->assertSame($expectedDatetime, $result->format('Y-m-d H:i:s'));
        $this->assertSame($expectedOffset, $result->format('P'));
    }

    /**
     * @return array<string, array{postgresValue: string, expectedDatetime: string, expectedOffset: string}>
     */
    public static function provideOffsetPreservations(): array
    {
        return [
            'without microseconds and UTC offset' => [
                'postgresValue' => '2023-06-15 10:30:45+00:00',
                'expectedDatetime' => '2023-06-15 10:30:45',
                'expectedOffset' => '+00:00',
            ],
            'positive offset' => [
                'postgresValue' => '2023-06-15 10:30:45.000000+02:00',
                'expectedDatetime' => '2023-06-15 10:30:45',
                'expectedOffset' => '+02:00',
            ],
            'negative offset' => [
                'postgresValue' => '2023-06-15 10:30:45.000000-05:00',
                'expectedDatetime' => '2023-06-15 10:30:45',
                'expectedOffset' => '-05:00',
            ],
        ];
    }

    /**
     * @return array<string, array{postgresValue: string, expectedFormattedValue: string}>
     */
    public static function provideBcEraAndExpandedYearTransformationsToPHP(): array
    {
        return [
            'first year of the BC era' => [
                'postgresValue' => '0001-01-15 10:30:00+00 BC',
                'expectedFormattedValue' => '+0000-01-15 10:30:00+00:00',
            ],
            'leap day of the first year of the BC era' => [
                'postgresValue' => '0001-02-29 10:30:00+00 BC',
                'expectedFormattedValue' => '+0000-02-29 10:30:00+00:00',
            ],
            'second year of the BC era' => [
                'postgresValue' => '0002-01-15 10:30:00+00 BC',
                'expectedFormattedValue' => '-0001-01-15 10:30:00+00:00',
            ],
            'five digit year' => [
                'postgresValue' => '10000-01-15 10:30:00+00',
                'expectedFormattedValue' => '+10000-01-15 10:30:00+00:00',
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
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s.uP', '+0000-01-15 10:30:00.000000+00:00'),
                'expectedPostgresValue' => '0001-01-15 10:30:00.000000+00:00 BC',
            ],
            'negative astronomical year' => [
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s.uP', '-0001-01-15 10:30:00.000000+00:00'),
                'expectedPostgresValue' => '0002-01-15 10:30:00.000000+00:00 BC',
            ],
            'five digit year' => [
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s.uP', '+10000-01-15 10:30:00.000000+00:00'),
                'expectedPostgresValue' => '10000-01-15 10:30:00.000000+00:00',
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
            'timestamp without time zone' => ['2023-06-15 10:30:45'],
            'empty string' => [''],
            'era suffix without a value' => [' BC'],
            'era suffix on a timestamp without time zone' => ['0001-01-15 10:30:00 BC'],
            'infinity spelled as a float' => ['inf'],
        ];
    }
}
