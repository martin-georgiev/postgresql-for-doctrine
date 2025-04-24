<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point as PointValueObject;

class DBALTypesIntegrationTest extends IntegrationTestCase
{
    /**
     * @dataProvider provideScalarTypeTestCases
     */
    public function test_scalar_type(string $typeName, string $columnType, mixed $testValue): void
    {
        $tableName = 'test_'.\str_replace(['[', ']', ' '], ['', '', '_'], $typeName);
        $columnName = 'test_column';

        try {
            $this->createTestTable($tableName, $columnName, $columnType);

            // Insert test value
            $queryBuilder = self::$connection->createQueryBuilder();
            $queryBuilder
                ->insert('test.'.$tableName)
                ->values([$columnName => '?'])
                ->setParameter(1, $testValue, $typeName);

            $queryBuilder->executeStatement();

            // Query the value back
            $queryBuilder = self::$connection->createQueryBuilder();
            $queryBuilder
                ->select($columnName)
                ->from('test.'.$tableName)
                ->where('id = 1');

            $result = $queryBuilder->executeQuery();
            $row = $result->fetchAssociative();
            \assert(\is_array($row) && \array_key_exists($columnName, $row));

            // Get the value with the correct type
            $platform = self::$connection->getDatabasePlatform();
            $retrievedValue = Type::getType($typeName)->convertToPHPValue($row[$columnName], $platform);

            // Assert the retrieved value matches the original
            if (\is_array($testValue)) {
                $this->assertEquals($testValue, $retrievedValue, 'Failed asserting that array values are equal for type '.$typeName);
            } else {
                $this->assertSame($testValue, $retrievedValue, 'Failed asserting that values are identical for type '.$typeName);
            }
        } finally {
            $this->dropTestTable($tableName);
        }
    }

    public static function provideScalarTypeTestCases(): array
    {
        return [
            'inet' => ['inet', 'INET', '192.168.1.1'],
            'inet with CIDR' => ['inet', 'INET', '192.168.1.0/24'],
            'inet IPv6' => ['inet', 'INET', '2001:db8::1'],
            'cidr IPv4' => ['cidr', 'CIDR', '192.168.1.0/24'],
            'cidr IPv6' => ['cidr', 'CIDR', '2001:db8::/32'],
            'macaddr' => ['macaddr', 'MACADDR', '08:00:2b:01:02:03'],
        ];
    }

    /**
     * @dataProvider provideArrayTypeTestCases
     */
    public function test_array_type(string $typeName, string $columnType, array $testValue): void
    {
        $tableName = 'test_'.\str_replace(['[', ']', ' '], ['', '', '_'], $typeName);
        $columnName = 'test_column';

        try {
            $this->createTestTable($tableName, $columnName, $columnType);

            // Insert test value
            $queryBuilder = self::$connection->createQueryBuilder();
            $queryBuilder
                ->insert('test.'.$tableName)
                ->values([$columnName => '?'])
                ->setParameter(1, $testValue, $typeName);

            $queryBuilder->executeStatement();

            // Query the value back
            $queryBuilder = self::$connection->createQueryBuilder();
            $queryBuilder
                ->select($columnName)
                ->from('test.'.$tableName)
                ->where('id = 1');

            $result = $queryBuilder->executeQuery();
            $row = $result->fetchAssociative();
            \assert(\is_array($row) && \array_key_exists($columnName, $row));

            // Get the value with the correct type
            $platform = self::$connection->getDatabasePlatform();
            $retrievedValue = Type::getType($typeName)->convertToPHPValue($row[$columnName], $platform);

            // Assert the retrieved value matches the original
            $this->assertEquals($testValue, $retrievedValue, 'Failed asserting that array values are equal for type '.$typeName);
        } finally {
            $this->dropTestTable($tableName);
        }
    }

    public static function provideArrayTypeTestCases(): array
    {
        return [
            'boolean[]' => ['boolean[]', 'BOOLEAN[]', [true, false, true]],
            'smallint[]' => ['smallint[]', 'SMALLINT[]', [32767, 0, -32768]],
            'integer[]' => ['integer[]', 'INTEGER[]', [1, 2, 3, 4, 5]],
            'bigint[]' => ['bigint[]', 'BIGINT[]', [9223372036854775807, 1, -9223372036854775807]],
            'text[]' => ['text[]', 'TEXT[]', ['foo', 'bar', 'baz']],
            'text[] with special chars' => ['text[]', 'TEXT[]', ['foo"bar', 'baz\qux', 'with,comma']],
            'real[]' => ['real[]', 'REAL[]', [1.5, 2.5, 3.5]],
            'double precision[]' => ['double precision[]', 'DOUBLE PRECISION[]', [1.123456789, 2.123456789, 3.123456789]],
            'cidr[]' => ['cidr[]', 'CIDR[]', ['192.168.1.0/24', '10.0.0.0/8', '172.16.0.0/16']],
            'inet[]' => ['inet[]', 'INET[]', ['192.168.1.1', '10.0.0.1', '172.16.0.1']],
            'macaddr[]' => ['macaddr[]', 'MACADDR[]', ['08:00:2b:01:02:03', '00:0c:29:aa:bb:cc']],
            'point[]' => ['point[]', 'POINT[]', [
                new PointValueObject(1.23, 4.56),
                new PointValueObject(-10.5, -20.75),
                new PointValueObject(0.0, 0.0),
            ]],
        ];
    }

    /**
     * @dataProvider provideJsonTypeTestCases
     */
    public function test_json_type(string $typeName, string $columnType, array $testValue): void
    {
        $tableName = 'test_'.\str_replace(['[', ']', ' '], ['', '', '_'], $typeName);
        $columnName = 'test_column';

        try {
            $this->createTestTable($tableName, $columnName, $columnType);

            // Insert test value
            $queryBuilder = self::$connection->createQueryBuilder();
            $queryBuilder
                ->insert('test.'.$tableName)
                ->values([$columnName => '?'])
                ->setParameter(1, $testValue, $typeName);

            $queryBuilder->executeStatement();

            // Query the value back
            $queryBuilder = self::$connection->createQueryBuilder();
            $queryBuilder
                ->select($columnName)
                ->from('test.'.$tableName)
                ->where('id = 1');

            $result = $queryBuilder->executeQuery();
            $row = $result->fetchAssociative();
            \assert(\is_array($row) && \array_key_exists($columnName, $row));

            // Get the value with the correct type
            $platform = self::$connection->getDatabasePlatform();
            $retrievedValue = Type::getType($typeName)->convertToPHPValue($row[$columnName], $platform);

            // Assert the retrieved value matches the original
            $this->assertEquals($testValue, $retrievedValue, 'Failed asserting that JSON values are equal for type '.$typeName);
        } finally {
            $this->dropTestTable($tableName);
        }
    }

    public static function provideJsonTypeTestCases(): array
    {
        return [
            'jsonb simple' => ['jsonb', 'JSONB', ['foo' => 'bar', 'baz' => 123]],
            'jsonb complex' => [
                'jsonb',
                'JSONB',
                [
                    'string' => 'value',
                    'number' => 42,
                    'boolean' => true,
                    'null' => null,
                    'array' => [1, 2, 3],
                    'object' => ['nested' => 'value'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider providePointTypeTestCases
     */
    public function test_point_type(string $typeName, string $columnType, PointValueObject $pointValueObject): void
    {
        $tableName = 'test_'.\str_replace(['[', ']', ' '], ['', '', '_'], $typeName);
        $columnName = 'test_column';

        try {
            $this->createTestTable($tableName, $columnName, $columnType);

            // Insert test value
            $queryBuilder = self::$connection->createQueryBuilder();
            $queryBuilder
                ->insert('test.'.$tableName)
                ->values([$columnName => '?'])
                ->setParameter(1, $pointValueObject, $typeName);

            $queryBuilder->executeStatement();

            // Query the value back
            $queryBuilder = self::$connection->createQueryBuilder();
            $queryBuilder
                ->select($columnName)
                ->from('test.'.$tableName)
                ->where('id = 1');

            $result = $queryBuilder->executeQuery();
            $row = $result->fetchAssociative();
            \assert(\is_array($row) && \array_key_exists($columnName, $row));

            // Get the value with the correct type
            $platform = self::$connection->getDatabasePlatform();
            $retrievedValue = Type::getType($typeName)->convertToPHPValue($row[$columnName], $platform);
            \assert($retrievedValue instanceof PointValueObject);

            // Assert the retrieved value matches the original
            $this->assertEquals($pointValueObject->getX(), $retrievedValue->getX(), 'Failed asserting that X coordinates are equal for type '.$typeName);
            $this->assertEquals($pointValueObject->getY(), $retrievedValue->getY(), 'Failed asserting that Y coordinates are equal for type '.$typeName);
        } finally {
            $this->dropTestTable($tableName);
        }
    }

    public static function providePointTypeTestCases(): array
    {
        return [
            'point with positive coordinates' => ['point', 'POINT', new PointValueObject(1.23, 4.56)],
            'point with negative coordinates' => ['point', 'POINT', new PointValueObject(-10.5, -20.75)],
            'point with zero coordinates' => ['point', 'POINT', new PointValueObject(0.0, 0.0)],
            'point with max precision' => ['point', 'POINT', new PointValueObject(123.456789, -98.765432)],
        ];
    }
}
