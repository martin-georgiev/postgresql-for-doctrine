<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BaseDateTimeArray;
use MartinGeorgiev\Doctrine\DBAL\Types\DateArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class DateArrayTest extends BaseDateTimeArrayTestCase
{
    protected function createFixture(): BaseDateTimeArray
    {
        return new DateArray();
    }

    protected function getExpectedTypeName(): string
    {
        return 'date[]';
    }

    protected static function getPHPExceptionClass(): string
    {
        return InvalidDateArrayItemForPHPException::class;
    }

    protected static function getDatabaseExceptionClass(): string
    {
        return InvalidDateArrayItemForDatabaseException::class;
    }

    #[DataProvider('provideValidItemTransformationsToPostgres')]
    #[Test]
    public function can_transform_date_item_for_postgres(\DateTimeInterface $phpValue, string $expectedPostgresValue): void
    {
        $this->assertSame($expectedPostgresValue, $this->fixture->convertToDatabaseValue([$phpValue], $this->platform));
    }

    /**
     * @return array<string, array{phpValue: \DateTimeInterface, expectedPostgresValue: string}>
     */
    public static function provideValidItemTransformationsToPostgres(): array
    {
        return [
            'DateTimeImmutable date' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15'),
                'expectedPostgresValue' => '{"2023-06-15"}',
            ],
            'DateTime date' => [
                'phpValue' => new \DateTimeImmutable('2000-01-01'),
                'expectedPostgresValue' => '{"2000-01-01"}',
            ],
            'leap day' => [
                'phpValue' => new \DateTimeImmutable('2024-02-29'),
                'expectedPostgresValue' => '{"2024-02-29"}',
            ],
            'multiple dates' => [
                'phpValue' => new \DateTimeImmutable('2023-12-31'),
                'expectedPostgresValue' => '{"2023-12-31"}',
            ],
        ];
    }

    #[DataProvider('provideValidItemTransformationsToPHP')]
    #[Test]
    public function can_transform_date_item_for_php(string $postgresValue, \DateTimeImmutable $expectedValue): void
    {
        $result = $this->fixture->transformArrayItemForPHP($postgresValue);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result);
        $this->assertSame($expectedValue->format('Y-m-d'), $result->format('Y-m-d'));
    }

    /**
     * @return array<string, array{postgresValue: string, expectedValue: \DateTimeImmutable}>
     */
    public static function provideValidItemTransformationsToPHP(): array
    {
        return [
            'standard date' => [
                'postgresValue' => '2023-06-15',
                'expectedValue' => new \DateTimeImmutable('2023-06-15'),
            ],
            'first day of year' => [
                'postgresValue' => '2000-01-01',
                'expectedValue' => new \DateTimeImmutable('2000-01-01'),
            ],
            'leap day' => [
                'postgresValue' => '2024-02-29',
                'expectedValue' => new \DateTimeImmutable('2024-02-29'),
            ],
            'last day of year' => [
                'postgresValue' => '2023-12-31',
                'expectedValue' => new \DateTimeImmutable('2023-12-31'),
            ],
        ];
    }

    #[Test]
    public function can_convert_multiple_dates_to_database(): void
    {
        $phpValue = [
            new \DateTimeImmutable('2023-06-15'),
            new \DateTimeImmutable('2024-02-29'),
        ];
        $this->assertSame('{"2023-06-15","2024-02-29"}', $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[Test]
    public function can_convert_multiple_dates_from_database(): void
    {
        $result = $this->fixture->convertToPHPValue('{2023-06-15,2024-02-29}', $this->platform);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result[0]);
        $this->assertInstanceOf(\DateTimeImmutable::class, $result[1]);
        $this->assertSame('2023-06-15', $result[0]->format('Y-m-d'));
        $this->assertSame('2024-02-29', $result[1]->format('Y-m-d'));
    }

    #[Test]
    public function can_validate_valid_array_item_for_database(): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(new \DateTimeImmutable('2023-06-15')));
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(new \DateTimeImmutable('2023-06-15')));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidFormatInputsForPHP(): array
    {
        return [
            'garbage string' => ['not-a-date'],
            'wrong format with time' => ['2023-06-15 10:30:00'],
            'US date format' => ['06/15/2023'],
            'empty string' => [''],
        ];
    }
}
