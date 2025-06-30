<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\DateRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange as DateRangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DateRangeTest extends TestCase
{
    private DateRange $fixture;

    private PostgreSQLPlatform $platform;

    protected function setUp(): void
    {
        $this->fixture = new DateRange();
        $this->platform = new PostgreSQLPlatform();
    }

    #[Test]
    public function can_get_sql_declaration(): void
    {
        $result = $this->fixture->getSQLDeclaration([], $this->platform);

        self::assertEquals('daterange', $result);
    }

    #[Test]
    #[DataProvider('providesValidPHPValues')]
    public function can_transform_from_php_value(DateRangeValueObject $dateRangeValueObject, string $expectedSqlValue): void
    {
        $result = $this->fixture->convertToDatabaseValue($dateRangeValueObject, $this->platform);

        self::assertEquals($expectedSqlValue, $result);
    }

    #[Test]
    public function can_transform_null_from_php_value(): void
    {
        $result = $this->fixture->convertToDatabaseValue(null, $this->platform);

        self::assertNull($result);
    }

    #[Test]
    #[DataProvider('providesValidSqlValues')]
    public function can_transform_from_sql_value(string $sqlValue, DateRangeValueObject $dateRangeValueObject): void
    {
        $result = $this->fixture->convertToPHPValue($sqlValue, $this->platform);

        self::assertEquals($dateRangeValueObject, $result);
    }

    #[Test]
    public function can_transform_null_from_sql_value(): void
    {
        $result = $this->fixture->convertToPHPValue(null, $this->platform);

        self::assertNull($result);
    }

    public static function providesValidPHPValues(): \Generator
    {
        yield 'simple range' => [
            new DateRangeValueObject(new \DateTimeImmutable('2023-01-01'), new \DateTimeImmutable('2023-12-31')),
            '[2023-01-01,2023-12-31)',
        ];
        yield 'year range' => [
            DateRangeValueObject::year(2023),
            '[2023-01-01,2024-01-01)',
        ];
        yield 'single day' => [
            DateRangeValueObject::singleDay(new \DateTimeImmutable('2023-06-15')),
            '[2023-06-15,2023-06-16)',
        ];
        yield 'empty range' => [
            DateRangeValueObject::empty(),
            'empty',
        ];
    }

    public static function providesValidSqlValues(): \Generator
    {
        yield 'simple range' => [
            '[2023-01-01,2023-12-31)',
            new DateRangeValueObject(new \DateTimeImmutable('2023-01-01'), new \DateTimeImmutable('2023-12-31')),
        ];
        yield 'empty range' => [
            'empty',
            DateRangeValueObject::empty(),
        ];
    }

    #[Test]
    public function can_handle_postgres_empty_range(): void
    {
        $result = $this->fixture->convertToPHPValue('empty', $this->platform);

        self::assertInstanceOf(DateRangeValueObject::class, $result);
        self::assertEquals('empty', (string) $result);
        self::assertTrue($result->isEmpty());
    }
}
