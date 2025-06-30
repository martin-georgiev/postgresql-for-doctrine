<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Int8Range;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range as Int8RangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class Int8RangeTest extends TestCase
{
    private Int8Range $fixture;

    private PostgreSQLPlatform $platform;

    protected function setUp(): void
    {
        $this->fixture = new Int8Range();
        $this->platform = new PostgreSQLPlatform();
    }

    #[Test]
    public function can_get_sql_declaration(): void
    {
        $result = $this->fixture->getSQLDeclaration([], $this->platform);

        self::assertEquals('int8range', $result);
    }

    #[Test]
    #[DataProvider('providesValidPHPValues')]
    public function can_transform_from_php_value(Int8RangeValueObject $int8RangeValueObject, string $expectedSqlValue): void
    {
        $result = $this->fixture->convertToDatabaseValue($int8RangeValueObject, $this->platform);

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
    public function can_transform_from_sql_value(string $sqlValue, Int8RangeValueObject $int8RangeValueObject): void
    {
        $result = $this->fixture->convertToPHPValue($sqlValue, $this->platform);

        self::assertEquals($int8RangeValueObject, $result);
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
            new Int8RangeValueObject(1, 1000),
            '[1,1000)',
        ];
        yield 'large range' => [
            new Int8RangeValueObject(PHP_INT_MIN, PHP_INT_MAX),
            '['.PHP_INT_MIN.','.PHP_INT_MAX.')',
        ];
        yield 'empty range' => [
            Int8RangeValueObject::empty(),
            'empty',
        ];
    }

    public static function providesValidSqlValues(): \Generator
    {
        yield 'simple range' => [
            '[1,1000)',
            new Int8RangeValueObject(1, 1000),
        ];
        yield 'empty range' => [
            'empty',
            Int8RangeValueObject::empty(),
        ];
    }

    #[Test]
    public function can_handle_postgres_empty_range(): void
    {
        $result = $this->fixture->convertToPHPValue('empty', $this->platform);

        self::assertInstanceOf(Int8RangeValueObject::class, $result);
        self::assertEquals('empty', (string) $result);
        self::assertTrue($result->isEmpty());
    }
}
