<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\DateArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class DateArrayTest extends BaseDateTimeArrayTestCase
{
    protected function createFixture(): DateArray
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

    protected static function getComparisonFormat(): string
    {
        return 'X-m-d';
    }

    #[DataProvider('provideValidItemTransformationsToPostgres')]
    #[Test]
    public function converts_date_item_for_postgres(\DateTimeInterface $phpValue, string $expectedPostgresValue): void
    {
        $this->assertSame($expectedPostgresValue, $this->fixture->convertToDatabaseValue([$phpValue], $this->platform));
    }

    /**
     * @return array<string, array{phpValue: \DateTimeInterface, expectedPostgresValue: string}>
     */
    public static function provideValidItemTransformationsToPostgres(): array
    {
        return [
            'DateTimeImmutable instance' => [
                'phpValue' => new \DateTimeImmutable('2023-06-15'),
                'expectedPostgresValue' => '{"2023-06-15"}',
            ],
            'DateTime instance' => [
                'phpValue' => self::createMutableDateTime('2000-01-01'),
                'expectedPostgresValue' => '{"2000-01-01"}',
            ],
        ];
    }

    #[DataProvider('provideValidItemTransformationsToPHP')]
    #[Test]
    public function converts_date_item_for_php(string $postgresValue, \DateTimeImmutable $expectedValue): void
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
        ];
    }

    #[Test]
    public function converts_multiple_dates_to_database_value(): void
    {
        $phpValue = [
            new \DateTimeImmutable('2023-06-15'),
            new \DateTimeImmutable('2024-02-29'),
        ];
        $this->assertSame('{"2023-06-15","2024-02-29"}', $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[Test]
    public function converts_multiple_dates_to_php_value(): void
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
    public function validates_valid_array_item_for_database(): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(new \DateTimeImmutable('2023-06-15')));
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase(self::createMutableDateTime('2023-06-15')));
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
                'expectedPostgresValue' => '{"0001-01-15 BC"}',
            ],
            'leap day of astronomical year zero' => [
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s', '+0000-02-29 00:00:00'),
                'expectedPostgresValue' => '{"0001-02-29 BC"}',
            ],
            'negative astronomical year' => [
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s', '-0001-01-15 00:00:00'),
                'expectedPostgresValue' => '{"0002-01-15 BC"}',
            ],
            'five digit year' => [
                'phpValue' => self::createImmutableDateTime('X-m-d H:i:s', '+10000-01-15 00:00:00'),
                'expectedPostgresValue' => '{"10000-01-15"}',
            ],
        ];
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
            'era suffix without a value' => [' BC'],
            'era suffix on a garbage string' => ['not-a-date BC'],
        ];
    }
}
