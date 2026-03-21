<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BaseDateTimeArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\TimestampTzArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class TimestampTzArrayTest extends BaseDateTimeArrayTestCase
{
    protected function createFixture(): BaseDateTimeArray
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

    #[DataProvider('provideValidItemTransformationsToPostgres')]
    #[Test]
    public function can_transform_timestamptz_item_for_postgres(\DateTimeInterface $phpValue, string $expectedPostgresValue): void
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
    public function can_transform_timestamptz_item_for_php(string $postgresValue, string $expectedDatetime, string $expectedOffset): void
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
    public function can_convert_multiple_timestamptz_values_to_database(): void
    {
        $phpValue = [
            new \DateTimeImmutable('2023-06-15 10:30:45+00:00'),
            new \DateTimeImmutable('2024-01-01 00:00:00+02:00'),
        ];
        $result = $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
        $this->assertSame('{"2023-06-15 10:30:45.000000+00:00","2024-01-01 00:00:00.000000+02:00"}', $result);
    }

    #[Test]
    public function can_convert_multiple_timestamptz_values_from_database(): void
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
    public function can_validate_valid_array_item_for_database(): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(new \DateTimeImmutable('2023-06-15 10:30:45+00:00')));
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(new \DateTimeImmutable('2023-06-15 10:30:45+02:00')));
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
        ];
    }
}
