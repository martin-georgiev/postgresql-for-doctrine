<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\TsRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange as TsRangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TsRangeTest extends TestCase
{
    private TsRange $fixture;

    private PostgreSQLPlatform $platform;

    protected function setUp(): void
    {
        $this->fixture = new TsRange();
        $this->platform = new PostgreSQLPlatform();
    }

    #[Test]
    public function can_get_sql_declaration(): void
    {
        $result = $this->fixture->getSQLDeclaration([], $this->platform);

        self::assertEquals('tsrange', $result);
    }

    #[Test]
    #[DataProvider('providesValidPHPValues')]
    public function can_transform_from_php_value(TsRangeValueObject $tsRangeValueObject, string $expectedSqlValue): void
    {
        $result = $this->fixture->convertToDatabaseValue($tsRangeValueObject, $this->platform);

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
    public function can_transform_from_sql_value(string $sqlValue, TsRangeValueObject $tsRangeValueObject): void
    {
        $result = $this->fixture->convertToPHPValue($sqlValue, $this->platform);

        self::assertEquals($tsRangeValueObject, $result);
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
            new TsRangeValueObject(
                new \DateTimeImmutable('2023-01-01 10:00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00')
            ),
            '[2023-01-01 10:00:00.000000,2023-01-01 18:00:00.000000)',
        ];
        yield 'hour range' => [
            TsRangeValueObject::hour(new \DateTimeImmutable('2023-01-01 14:30:00')),
            '[2023-01-01 14:00:00.000000,2023-01-01 15:00:00.000000)',
        ];
        yield 'empty range' => [
            TsRangeValueObject::empty(),
            'empty',
        ];
    }

    public static function providesValidSqlValues(): \Generator
    {
        yield 'simple range' => [
            '[2023-01-01 10:00:00,2023-01-01 18:00:00)',
            new TsRangeValueObject(
                new \DateTimeImmutable('2023-01-01 10:00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00')
            ),
        ];
        yield 'empty range' => [
            'empty',
            TsRangeValueObject::empty(),
        ];
    }

    #[Test]
    public function can_handle_postgres_empty_range(): void
    {
        $result = $this->fixture->convertToPHPValue('empty', $this->platform);

        self::assertInstanceOf(TsRangeValueObject::class, $result);
        self::assertEquals('empty', (string) $result);
        self::assertTrue($result->isEmpty());
    }
}
