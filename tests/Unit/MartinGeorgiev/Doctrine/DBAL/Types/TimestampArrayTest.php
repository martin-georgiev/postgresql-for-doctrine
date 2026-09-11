<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\TimestampArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class TimestampArrayTest extends BaseDateTimeArrayTestCase
{
    protected function createFixture(): TimestampArray
    {
        return new TimestampArray();
    }

    protected function getExpectedTypeName(): string
    {
        return 'timestamp[]';
    }

    protected static function getPHPExceptionClass(): string
    {
        return InvalidTimestampArrayItemForPHPException::class;
    }

    protected static function getDatabaseExceptionClass(): string
    {
        return InvalidTimestampArrayItemForDatabaseException::class;
    }

    protected static function getComparisonFormat(): string
    {
        return 'X-m-d H:i:s';
    }

    #[DataProvider('provideValidItemTransformationsToPostgres')]
    #[Test]
    public function converts_timestamp_item_to_database_value(\DateTimeInterface $phpValue, string $expectedPostgresValue): void
    {
        $this->assertSame($expectedPostgresValue, $this->fixture->convertToDatabaseValue([$phpValue], $this->platform));
    }

    /**
     * @return array<string, array{phpValue: \DateTimeInterface, expectedPostgresValue: string}>
     */
    public static function provideValidItemTransformationsToPostgres(): array
    {
        $midnight = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:00');
        \assert($midnight instanceof \DateTimeImmutable);

        return [
            'with microseconds' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15 10:30:45.123456'),
                'expectedPostgresValue' => '{"2023-06-15 10:30:45.123456"}',
            ],
            'without microseconds' => [
                'phpValue' => $midnight,
                'expectedPostgresValue' => '{"2000-01-01 00:00:00.000000"}',
            ],
        ];
    }

    #[DataProvider('provideValidItemTransformationsToPHP')]
    #[Test]
    public function converts_timestamp_item_to_php_value(string $postgresValue, string $expectedDatetime): void
    {
        $result = $this->fixture->transformArrayItemForPHP($postgresValue);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result);
        $this->assertSame($expectedDatetime, $result->format('Y-m-d H:i:s'));
    }

    /**
     * @return array<string, array{postgresValue: string, expectedDatetime: string}>
     */
    public static function provideValidItemTransformationsToPHP(): array
    {
        return [
            'timestamp with microseconds' => [
                'postgresValue' => '2023-06-15 10:30:45.123456',
                'expectedDatetime' => '2023-06-15 10:30:45',
            ],
            'timestamp without microseconds' => [
                'postgresValue' => '2023-06-15 10:30:45',
                'expectedDatetime' => '2023-06-15 10:30:45',
            ],
        ];
    }

    #[Test]
    public function converts_multiple_timestamps_to_database_value(): void
    {
        $first = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2023-06-15 10:30:45');
        $second = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2024-01-01 00:00:00');
        \assert($first instanceof \DateTimeImmutable);
        \assert($second instanceof \DateTimeImmutable);

        $result = $this->fixture->convertToDatabaseValue([$first, $second], $this->platform);
        $this->assertSame('{"2023-06-15 10:30:45.000000","2024-01-01 00:00:00.000000"}', $result);
    }

    #[Test]
    public function converts_multiple_timestamps_to_php_value(): void
    {
        $result = $this->fixture->convertToPHPValue('{"2023-06-15 10:30:45","2024-01-01 00:00:00"}', $this->platform);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result[0]);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result[1]);
        $this->assertSame('2023-06-15 10:30:45', $result[0]->format('Y-m-d H:i:s'));
        $this->assertSame('2024-01-01 00:00:00', $result[1]->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function validates_valid_array_item_for_database(): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(new \DateTimeImmutable('2023-06-15 10:30:45')));
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(new \DateTimeImmutable('2024-01-01 00:00:00')));
    }

    /**
     * The PostgreSQL values are the ones a PostgreSQL 18 server emits for
     * `ARRAY['0001-01-15 10:30:00 BC', '0001-02-29 10:30:00 BC', '10000-01-15 10:30:00']::timestamp[]`.
     *
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
            'microseconds in the BC era' => [
                'postgresValue' => '0001-01-15 10:30:00.123456 BC',
                'expectedFormattedValue' => '+0000-01-15 10:30:00',
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
                'expectedPostgresValue' => '{"0001-01-15 10:30:00.000000 BC"}',
            ],
            'leap day of astronomical year zero' => [
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s.u', '+0000-02-29 10:30:00.000000'),
                'expectedPostgresValue' => '{"0001-02-29 10:30:00.000000 BC"}',
            ],
            'negative astronomical year' => [
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s.u', '-0001-01-15 10:30:00.000000'),
                'expectedPostgresValue' => '{"0002-01-15 10:30:00.000000 BC"}',
            ],
            'five digit year' => [
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s.u', '+10000-01-15 10:30:00.000000'),
                'expectedPostgresValue' => '{"10000-01-15 10:30:00.000000"}',
            ],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidFormatItemsFromDatabase(): array
    {
        return [
            'garbage string' => ['not-a-timestamp'],
            'date only' => ['2023-06-15'],
            'empty string' => [''],
        ];
    }
}
