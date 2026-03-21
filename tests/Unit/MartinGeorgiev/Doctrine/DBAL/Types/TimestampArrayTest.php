<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BaseDateTimeArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\TimestampArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class TimestampArrayTest extends BaseDateTimeArrayTestCase
{
    protected function createFixture(): BaseDateTimeArray
    {
        return new TimestampArray();
    }

    protected function getExpectedTypeName(): string
    {
        return 'timestamp[]';
    }

    protected static function getPhpExceptionClass(): string
    {
        return InvalidTimestampArrayItemForPHPException::class;
    }

    protected static function getDatabaseExceptionClass(): string
    {
        return InvalidTimestampArrayItemForDatabaseException::class;
    }

    #[DataProvider('provideValidItemTransformationsToPostgres')]
    #[Test]
    public function can_transform_timestamp_item_for_postgres(\DateTimeInterface $phpValue, string $expectedPostgresValue): void
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
            'DateTimeImmutable with microseconds' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15 10:30:45.123456'),
                'expectedPostgresValue' => '{"2023-06-15 10:30:45.123456"}',
            ],
            'DateTime without microseconds' => [
                'phpValue' => $midnight,
                'expectedPostgresValue' => '{"2000-01-01 00:00:00.000000"}',
            ],
        ];
    }

    #[DataProvider('provideValidItemTransformationsToPHP')]
    #[Test]
    public function can_transform_timestamp_item_for_php(string $postgresValue, string $expectedDatetime): void
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
            'midnight timestamp' => [
                'postgresValue' => '2000-01-01 00:00:00',
                'expectedDatetime' => '2000-01-01 00:00:00',
            ],
        ];
    }

    #[Test]
    public function can_convert_multiple_timestamps_to_database(): void
    {
        $first = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2023-06-15 10:30:45');
        $second = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2024-01-01 00:00:00');
        \assert($first instanceof \DateTimeImmutable);
        \assert($second instanceof \DateTimeImmutable);

        $result = $this->fixture->convertToDatabaseValue([$first, $second], $this->platform);
        $this->assertSame('{"2023-06-15 10:30:45.000000","2024-01-01 00:00:00.000000"}', $result);
    }

    #[Test]
    public function can_convert_multiple_timestamps_from_database(): void
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
    public function can_validate_valid_array_item_for_database(): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(new \DateTimeImmutable('2023-06-15 10:30:45')));
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(new \DateTimeImmutable('2023-06-15 10:30:45')));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidFormatInputsForPHP(): array
    {
        return [
            'garbage string' => ['not-a-timestamp'],
            'date only' => ['2023-06-15'],
            'empty string' => [''],
        ];
    }
}
