<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\DateRange as DateRangeType;
use MartinGeorgiev\Doctrine\DBAL\Types\Int4Range as Int4RangeType;
use MartinGeorgiev\Doctrine\DBAL\Types\Int8Range as Int8RangeType;
use MartinGeorgiev\Doctrine\DBAL\Types\NumRange as NumRangeType;
use MartinGeorgiev\Doctrine\DBAL\Types\TsRange as TsRangeType;
use MartinGeorgiev\Doctrine\DBAL\Types\TstzRange as TstzRangeType;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange as DateRangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range as Int4RangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range as Int8RangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange as NumRangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange as TsRangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzRange as TstzRangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class RangeTypesIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Type::hasType('numrange')) {
            Type::addType('numrange', NumRangeType::class);
        }

        if (!Type::hasType('int4range')) {
            Type::addType('int4range', Int4RangeType::class);
        }

        if (!Type::hasType('int8range')) {
            Type::addType('int8range', Int8RangeType::class);
        }

        if (!Type::hasType('daterange')) {
            Type::addType('daterange', DateRangeType::class);
        }

        if (!Type::hasType('tsrange')) {
            Type::addType('tsrange', TsRangeType::class);
        }

        if (!Type::hasType('tstzrange')) {
            Type::addType('tstzrange', TstzRangeType::class);
        }
    }

    #[Test]
    #[DataProvider('providesRangeTypesAndValues')]
    public function can_store_and_retrieve_range_values(string $typeName, string $columnType, mixed $value): void
    {
        $tableName = 'test_range_'.$typeName;
        $columnName = 'range_value';

        $this->createTestTableForDataType($tableName, $columnName, $columnType);

        // Insert the value
        $sql = \sprintf(
            'INSERT INTO %s.%s ("%s") VALUES (?)',
            self::DATABASE_SCHEMA,
            $tableName,
            $columnName
        );
        $this->connection->executeStatement($sql, [$value], [$typeName]);

        // Retrieve the value
        $sql = \sprintf(
            'SELECT "%s" FROM %s.%s WHERE id = 1',
            $columnName,
            self::DATABASE_SCHEMA,
            $tableName
        );
        $result = $this->connection->fetchOne($sql);

        $type = Type::getType($typeName);
        $convertedResult = $type->convertToPHPValue($result, $this->connection->getDatabasePlatform());

        self::assertEquals($value, $convertedResult);
    }

    public static function providesRangeTypesAndValues(): \Generator
    {
        yield 'numrange simple' => ['numrange', 'NUMRANGE', new NumRangeValueObject(1.5, 10.7)];
        yield 'numrange infinite' => ['numrange', 'NUMRANGE', new NumRangeValueObject(null, 1000, false, false)];
        yield 'numrange empty' => ['numrange', 'NUMRANGE', NumRangeValueObject::empty()];

        yield 'int4range simple' => ['int4range', 'INT4RANGE', new Int4RangeValueObject(1, 1000)];
        yield 'int4range infinite' => ['int4range', 'INT4RANGE', new Int4RangeValueObject(null, 1000, false, false)];
        yield 'int8range simple' => ['int8range', 'INT8RANGE', new Int8RangeValueObject(PHP_INT_MIN, PHP_INT_MAX)];

        yield 'daterange simple' => ['daterange', 'DATERANGE', new DateRangeValueObject(new \DateTimeImmutable('2023-01-01'), new \DateTimeImmutable('2023-12-31'))];
        yield 'tsrange simple' => ['tsrange', 'TSRANGE', new TsRangeValueObject(new \DateTimeImmutable('2023-01-01 10:00:00'), new \DateTimeImmutable('2023-01-01 18:00:00'))];
        yield 'tstzrange simple' => ['tstzrange', 'TSTZRANGE', new TstzRangeValueObject(new \DateTimeImmutable('2023-01-01 10:00:00+00:00'), new \DateTimeImmutable('2023-01-01 18:00:00+00:00'))];
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $this->createTestTableForDataType('test_null_range', 'range_value', 'NUMRANGE');

        $sql = \sprintf(
            'INSERT INTO %s.test_null_range (range_value) VALUES (?)',
            self::DATABASE_SCHEMA
        );
        $this->connection->executeStatement($sql, [null], ['numrange']);

        $sql = \sprintf(
            'SELECT range_value FROM %s.test_null_range WHERE id = 1',
            self::DATABASE_SCHEMA
        );
        $result = $this->connection->fetchOne($sql);

        $type = Type::getType('numrange');
        $convertedResult = $type->convertToPHPValue($result, $this->connection->getDatabasePlatform());

        self::assertNull($convertedResult);
    }
}
