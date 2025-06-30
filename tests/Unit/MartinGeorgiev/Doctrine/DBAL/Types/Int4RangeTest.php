<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Int4Range;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range as Int4RangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class Int4RangeTest extends TestCase
{
    private Int4Range $fixture;

    private PostgreSQLPlatform $platform;

    protected function setUp(): void
    {
        $this->fixture = new Int4Range();
        $this->platform = new PostgreSQLPlatform();
    }

    #[Test]
    public function can_get_sql_declaration(): void
    {
        $result = $this->fixture->getSQLDeclaration([], $this->platform);

        self::assertEquals('int4range', $result);
    }

    #[Test]
    #[DataProvider('providesValidPHPValues')]
    public function can_transform_from_php_value(Int4RangeValueObject $int4RangeValueObject, string $expectedSqlValue): void
    {
        $result = $this->fixture->convertToDatabaseValue($int4RangeValueObject, $this->platform);

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
    public function can_transform_from_sql_value(string $sqlValue, Int4RangeValueObject $int4RangeValueObject): void
    {
        $result = $this->fixture->convertToPHPValue($sqlValue, $this->platform);

        self::assertEquals($int4RangeValueObject, $result);
    }

    #[Test]
    public function can_transform_null_from_sql_value(): void
    {
        $result = $this->fixture->convertToPHPValue(null, $this->platform);

        self::assertNull($result);
    }

    #[Test]
    public function can_transform_empty_string_from_sql_value(): void
    {
        $result = $this->fixture->convertToPHPValue('', $this->platform);

        self::assertNull($result);
    }

    public static function providesValidPHPValues(): \Generator
    {
        yield 'simple range' => [
            new Int4RangeValueObject(1, 1000),
            '[1,1000)',
        ];
        yield 'inclusive range' => [
            new Int4RangeValueObject(1, 10, true, true),
            '[1,10]',
        ];
        yield 'exclusive range' => [
            new Int4RangeValueObject(1, 10, false, false),
            '(1,10)',
        ];
        yield 'lower infinite' => [
            new Int4RangeValueObject(null, 10),
            '[,10)',
        ];
        yield 'upper infinite' => [
            new Int4RangeValueObject(1, null),
            '[1,)',
        ];
        yield 'empty range' => [
            Int4RangeValueObject::empty(),
            'empty',
        ];
        yield 'max int4 values' => [
            new Int4RangeValueObject(-2147483648, 2147483647),
            '[-2147483648,2147483647)',
        ];
    }

    public static function providesValidSqlValues(): \Generator
    {
        yield 'simple range' => [
            '[1,1000)',
            new Int4RangeValueObject(1, 1000),
        ];
        yield 'inclusive range' => [
            '[1,10]',
            new Int4RangeValueObject(1, 10, true, true),
        ];
        yield 'exclusive range' => [
            '(1,10)',
            new Int4RangeValueObject(1, 10, false, false),
        ];
        yield 'lower infinite' => [
            '[,10)',
            new Int4RangeValueObject(null, 10),
        ];
        yield 'upper infinite' => [
            '[1,)',
            new Int4RangeValueObject(1, null),
        ];
        yield 'empty range' => [
            'empty',
            Int4RangeValueObject::empty(),
        ];
    }

    #[Test]
    public function can_handle_postgres_empty_range(): void
    {
        $result = $this->fixture->convertToPHPValue('empty', $this->platform);

        self::assertInstanceOf(Int4RangeValueObject::class, $result);
        self::assertEquals('empty', (string) $result);
        self::assertTrue($result->isEmpty());
    }
}
