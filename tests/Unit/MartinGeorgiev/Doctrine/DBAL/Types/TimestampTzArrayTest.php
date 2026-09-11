<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\TimestampTzArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class TimestampTzArrayTest extends BaseDateTimeArrayTestCase
{
    protected function createFixture(): TimestampTzArray
    {
        return new TimestampTzArray();
    }

    protected function getExpectedTypeName(): string
    {
        return 'timestamptz[]';
    }

    protected static function getPHPExceptionClass(): string
    {
        return InvalidTimestampTzArrayItemForPHPException::class;
    }

    protected static function getDatabaseExceptionClass(): string
    {
        return InvalidTimestampTzArrayItemForDatabaseException::class;
    }

    protected static function getComparisonFormat(): string
    {
        return 'X-m-d H:i:sP';
    }

    #[DataProvider('provideValidItemTransformationsToPostgres')]
    #[Test]
    public function converts_timestamptz_item_for_postgres(\DateTimeInterface $phpValue, string $expectedPostgresValue): void
    {
        $this->assertSame($expectedPostgresValue, $this->fixture->convertToDatabaseValue([$phpValue], $this->platform));
    }

    /**
     * @return array<string, array{phpValue: \DateTimeInterface, expectedPostgresValue: string}>
     */
    public static function provideValidItemTransformationsToPostgres(): array
    {
        return [
            'UTC offset' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15 10:30:45+00:00'),
                'expectedPostgresValue' => '{"2023-06-15 10:30:45.000000+00:00"}',
            ],
            'positive offset' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15 10:30:45+02:00'),
                'expectedPostgresValue' => '{"2023-06-15 10:30:45.000000+02:00"}',
            ],
            'negative offset' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15 10:30:45-05:00'),
                'expectedPostgresValue' => '{"2023-06-15 10:30:45.000000-05:00"}',
            ],
        ];
    }

    #[DataProvider('provideValidItemTransformationsToPHP')]
    #[Test]
    public function converts_timestamptz_item_for_php(string $postgresValue, string $expectedDatetime, string $expectedOffset): void
    {
        $result = $this->fixture->transformArrayItemForPHP($postgresValue);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result);
        $this->assertSame($expectedDatetime, $result->format('Y-m-d H:i:s'));
        $this->assertSame($expectedOffset, $result->format('P'));
    }

    /**
     * @return array<string, array{postgresValue: string, expectedDatetime: string, expectedOffset: string}>
     */
    public static function provideValidItemTransformationsToPHP(): array
    {
        return [
            'with microseconds and UTC offset' => [
                'postgresValue' => '2023-06-15 10:30:45.000000+00:00',
                'expectedDatetime' => '2023-06-15 10:30:45',
                'expectedOffset' => '+00:00',
            ],
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

    #[Test]
    public function converts_multiple_timestamptz_values_to_database_value(): void
    {
        $phpValue = [
            new \DateTimeImmutable('2023-06-15 10:30:45+00:00'),
            new \DateTimeImmutable('2024-01-01 00:00:00+02:00'),
        ];
        $result = $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
        $this->assertSame('{"2023-06-15 10:30:45.000000+00:00","2024-01-01 00:00:00.000000+02:00"}', $result);
    }

    #[Test]
    public function converts_multiple_timestamptz_values_to_php_value(): void
    {
        $result = $this->fixture->convertToPHPValue('{"2023-06-15 10:30:45.000000+00:00","2024-01-01 00:00:00.000000+02:00"}', $this->platform);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result[0]);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result[1]);
        $this->assertSame('2023-06-15 10:30:45', $result[0]->format('Y-m-d H:i:s'));
        $this->assertSame('+00:00', $result[0]->format('P'));
        $this->assertSame('2024-01-01 00:00:00', $result[1]->format('Y-m-d H:i:s'));
        $this->assertSame('+02:00', $result[1]->format('P'));
    }

    #[Test]
    public function validates_valid_array_item_for_database(): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(new \DateTimeImmutable('2023-06-15 10:30:45+00:00')));
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(new \DateTimeImmutable('2023-06-15 10:30:45+02:00')));
    }

    /**
     * The PostgreSQL values are the ones a PostgreSQL 18 server emits for
     * `ARRAY['0001-01-15 10:30:00 BC', '10000-01-15 10:30:00']::timestamptz[]` — it prints the
     * offset before the era suffix, not after it.
     *
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
            'microseconds in the BC era' => [
                'postgresValue' => '0001-01-15 10:30:00.123456+00 BC',
                'expectedFormattedValue' => '+0000-01-15 10:30:00+00:00',
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
                'expectedPostgresValue' => '{"0001-01-15 10:30:00.000000+00:00 BC"}',
            ],
            'negative astronomical year' => [
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s.uP', '-0001-01-15 10:30:00.000000+00:00'),
                'expectedPostgresValue' => '{"0002-01-15 10:30:00.000000+00:00 BC"}',
            ],
            'five digit year' => [
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s.uP', '+10000-01-15 10:30:00.000000+00:00'),
                'expectedPostgresValue' => '{"10000-01-15 10:30:00.000000+00:00"}',
            ],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidFormatInputsForPHP(): array
    {
        return [
            'garbage string' => ['not-a-timestamp'],
            'date only' => ['2023-06-15'],
            'timestamp without timezone' => ['2023-06-15 10:30:45'],
            'empty string' => [''],
            'era suffix without a value' => [' BC'],
            'era suffix on a timestamp without timezone' => ['0001-01-15 10:30:00 BC'],
        ];
    }
}
