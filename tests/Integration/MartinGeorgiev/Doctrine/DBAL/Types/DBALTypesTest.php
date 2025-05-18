<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point as PointValueObject;
use PHPUnit\Framework\Attributes\DataProvider;

class DBALTypesTest extends TestCase
{
    #[DataProvider('provideScalarTypeTestCases')]
    public function test_scalar_type(string $typeName, string $columnType, mixed $testValue): void
    {
        $this->runTypeTest($typeName, $columnType, $testValue);
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

    #[DataProvider('provideArrayTypeTestCases')]
    public function test_array_type(string $typeName, string $columnType, array $testValue): void
    {
        $this->runTypeTest($typeName, $columnType, $testValue);
    }

    public static function provideArrayTypeTestCases(): array
    {
        return [
            'bool[]' => ['bool[]', 'BOOL[]', [true, false, true]],
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

    #[DataProvider('provideJsonTypeTestCases')]
    public function test_json_type(string $typeName, string $columnType, array $testValue): void
    {
        $this->runTypeTest($typeName, $columnType, $testValue);
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

    #[DataProvider('providePointTypeTestCases')]
    public function test_point_type(string $typeName, string $columnType, PointValueObject $pointValueObject): void
    {
        $this->runTypeTest($typeName, $columnType, $pointValueObject);
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

    /**
     * Generic test method that handles all types of tests.
     */
    private function runTypeTest(string $typeName, string $columnType, mixed $testValue): void
    {
        $tableName = 'test_'.\str_replace(['[', ']', ' '], ['', '', '_'], $typeName);
        $columnName = 'test_column';

        try {
            $this->createTestTableForDataType($tableName, $columnName, $columnType);

            // Insert test value
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->insert('test.'.$tableName)
                ->values([$columnName => ':value'])
                ->setParameter('value', $testValue, $typeName);

            $queryBuilder->executeStatement();

            // Query the value back
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->select($columnName)
                ->from('test.'.$tableName)
                ->where('id = 1');

            $result = $queryBuilder->executeQuery();
            $row = $result->fetchAssociative();
            \assert(\is_array($row) && \array_key_exists($columnName, $row));

            // Get the value with the correct type
            $platform = $this->connection->getDatabasePlatform();
            $retrievedValue = Type::getType($typeName)->convertToPHPValue($row[$columnName], $platform);

            $this->assertDatabaseRoundtripEquals($testValue, $retrievedValue, $typeName);
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }

    private function assertDatabaseRoundtripEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        match (true) {
            $expected instanceof PointValueObject => $this->assertPointEquals($expected, $actual, $typeName),
            \is_array($expected) => $this->assertEquals($expected, $actual, 'Failed asserting that array values are equal for type '.$typeName),
            default => $this->assertSame($expected, $actual, 'Failed asserting that values are identical for type '.$typeName)
        };
    }

    private function assertPointEquals(PointValueObject $pointValueObject, mixed $actual, string $typeName): void
    {
        $this->assertInstanceOf(PointValueObject::class, $actual, 'Failed asserting that value is a Point object for type '.$typeName);
        $this->assertEquals($pointValueObject->getX(), $actual->getX(), 'Failed asserting that X coordinates are equal for type '.$typeName);
        $this->assertEquals($pointValueObject->getY(), $actual->getY(), 'Failed asserting that Y coordinates are equal for type '.$typeName);
    }
}
