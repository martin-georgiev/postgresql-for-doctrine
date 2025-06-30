<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\TstzRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzRange as TstzRangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TstzRangeTest extends TestCase
{
    private TstzRange $fixture;

    private PostgreSQLPlatform $platform;

    protected function setUp(): void
    {
        $this->fixture = new TstzRange();
        $this->platform = new PostgreSQLPlatform();
    }

    #[Test]
    public function can_get_sql_declaration(): void
    {
        $result = $this->fixture->getSQLDeclaration([], $this->platform);

        self::assertEquals('tstzrange', $result);
    }

    #[Test]
    #[DataProvider('providesValidPHPValues')]
    public function can_transform_from_php_value(TstzRangeValueObject $tstzRangeValueObject, string $expectedSqlValue): void
    {
        $result = $this->fixture->convertToDatabaseValue($tstzRangeValueObject, $this->platform);

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
    public function can_transform_from_sql_value(string $sqlValue, TstzRangeValueObject $tstzRangeValueObject): void
    {
        $result = $this->fixture->convertToPHPValue($sqlValue, $this->platform);

        self::assertEquals($tstzRangeValueObject, $result);
    }

    #[Test]
    public function can_transform_null_from_sql_value(): void
    {
        $result = $this->fixture->convertToPHPValue(null, $this->platform);

        self::assertNull($result);
    }

    public static function providesValidPHPValues(): \Generator
    {
        yield 'simple range with timezone' => [
            new TstzRangeValueObject(
                new \DateTimeImmutable('2023-01-01 10:00:00+00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00+00:00')
            ),
            '[2023-01-01 10:00:00.000000+00:00,2023-01-01 18:00:00.000000+00:00)',
        ];
        yield 'empty range' => [
            TstzRangeValueObject::empty(),
            'empty',
        ];
    }

    public static function providesValidSqlValues(): \Generator
    {
        yield 'simple range with timezone' => [
            '[2023-01-01 10:00:00+00:00,2023-01-01 18:00:00+00:00)',
            new TstzRangeValueObject(
                new \DateTimeImmutable('2023-01-01 10:00:00+00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00+00:00')
            ),
        ];
        yield 'empty range' => [
            'empty',
            TstzRangeValueObject::empty(),
        ];
    }

    #[Test]
    public function can_handle_postgres_empty_range(): void
    {
        $result = $this->fixture->convertToPHPValue('empty', $this->platform);

        self::assertInstanceOf(TstzRangeValueObject::class, $result);
        self::assertEquals('empty', (string) $result);
        self::assertTrue($result->isEmpty());
    }
}
