<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\NumRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NumRangeTest extends TestCase
{
    private NumRange $fixture;

    private PostgreSQLPlatform $platform;

    protected function setUp(): void
    {
        $this->fixture = new NumRange();
        $this->platform = new PostgreSQLPlatform();
    }

    #[Test]
    public function can_get_sql_declaration(): void
    {
        $result = $this->fixture->getSQLDeclaration([], $this->platform);

        self::assertEquals('numrange', $result);
    }

    #[Test]
    #[DataProvider('providesValidPHPValues')]
    public function can_transform_from_php_value(NumericRange $numericRange, string $expectedSqlValue): void
    {
        $result = $this->fixture->convertToDatabaseValue($numericRange, $this->platform);

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
    public function can_transform_from_sql_value(string $sqlValue, NumericRange $numericRange): void
    {
        $result = $this->fixture->convertToPHPValue($sqlValue, $this->platform);

        self::assertEquals($numericRange, $result);
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
            new NumericRange(1.5, 10.7),
            '[1.5,10.7)',
        ];
        yield 'inclusive range' => [
            new NumericRange(1, 10, true, true),
            '[1,10]',
        ];
        yield 'exclusive range' => [
            new NumericRange(1, 10, false, false),
            '(1,10)',
        ];
        yield 'lower infinite' => [
            new NumericRange(null, 10),
            '[,10)',
        ];
        yield 'upper infinite' => [
            new NumericRange(1, null),
            '[1,)',
        ];
        yield 'empty range' => [
            NumericRange::empty(),
            'empty',
        ];
    }

    public static function providesValidSqlValues(): \Generator
    {
        yield 'simple range' => [
            '[1.5,10.7)',
            new NumericRange(1.5, 10.7),
        ];
        yield 'inclusive range' => [
            '[1,10]',
            new NumericRange(1, 10, true, true),
        ];
        yield 'exclusive range' => [
            '(1,10)',
            new NumericRange(1, 10, false, false),
        ];
        yield 'lower infinite' => [
            '[,10)',
            new NumericRange(null, 10),
        ];
        yield 'upper infinite' => [
            '[1,)',
            new NumericRange(1, null),
        ];
        yield 'empty range' => [
            'empty',
            NumericRange::empty(),
        ];
    }

    #[Test]
    public function can_handle_postgres_empty_range(): void
    {
        $result = $this->fixture->convertToPHPValue('empty', $this->platform);

        self::assertInstanceOf(NumericRange::class, $result);
        self::assertEquals('empty', (string) $result);
    }
}
