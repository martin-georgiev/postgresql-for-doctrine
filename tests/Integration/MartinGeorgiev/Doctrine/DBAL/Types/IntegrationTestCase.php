<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray;
use MartinGeorgiev\Doctrine\DBAL\Types\BooleanArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Cidr;
use MartinGeorgiev\Doctrine\DBAL\Types\CidrArray;
use MartinGeorgiev\Doctrine\DBAL\Types\DoublePrecisionArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Inet;
use MartinGeorgiev\Doctrine\DBAL\Types\InetArray;
use MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Jsonb;
use MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Macaddr;
use MartinGeorgiev\Doctrine\DBAL\Types\MacaddrArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Point;
use MartinGeorgiev\Doctrine\DBAL\Types\PointArray;
use MartinGeorgiev\Doctrine\DBAL\Types\RealArray;
use MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray;
use MartinGeorgiev\Doctrine\DBAL\Types\TextArray;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected static Connection $connection;

    protected static array $registeredTypes = [];

    public static function setUpBeforeClass(): void
    {
        // Get environment variables with proper type casting
        /** @phpstan-ignore-next-line */
        $host = isset($_ENV['POSTGRES_HOST']) ? (string) $_ENV['POSTGRES_HOST'] : 'localhost';
        /** @phpstan-ignore-next-line */
        $port = isset($_ENV['POSTGRES_PORT']) ? (int) $_ENV['POSTGRES_PORT'] : 5432;
        /** @phpstan-ignore-next-line */
        $dbname = isset($_ENV['POSTGRES_DB']) ? (string) $_ENV['POSTGRES_DB'] : 'postgres_doctrine_test';
        /** @phpstan-ignore-next-line */
        $user = isset($_ENV['POSTGRES_USER']) ? (string) $_ENV['POSTGRES_USER'] : 'postgres';
        /** @phpstan-ignore-next-line */
        $password = isset($_ENV['POSTGRES_PASSWORD']) ? (string) $_ENV['POSTGRES_PASSWORD'] : 'postgres';

        $connectionParams = [
            'driver' => 'pdo_pgsql',
            'host' => $host,
            'port' => $port,
            'dbname' => $dbname,
            'user' => $user,
            'password' => $password,
        ];

        self::$connection = DriverManager::getConnection($connectionParams);

        self::registerCustomTypes();
    }

    public static function tearDownAfterClass(): void
    {
        if (isset(self::$connection)) {
            self::$connection->close();
        }
    }

    protected static function registerCustomTypes(): void
    {
        $typesMap = [
            'bigint[]' => BigIntArray::class,
            'boolean[]' => BooleanArray::class,
            'cidr' => Cidr::class,
            'cidr[]' => CidrArray::class,
            'double precision[]' => DoublePrecisionArray::class,
            'inet' => Inet::class,
            'inet[]' => InetArray::class,
            'integer[]' => IntegerArray::class,
            'jsonb' => Jsonb::class,
            'jsonb[]' => JsonbArray::class,
            'macaddr' => Macaddr::class,
            'macaddr[]' => MacaddrArray::class,
            'point' => Point::class,
            'point[]' => PointArray::class,
            'real[]' => RealArray::class,
            'smallint[]' => SmallIntArray::class,
            'text[]' => TextArray::class,
        ];

        foreach ($typesMap as $typeName => $typeClass) {
            if (!Type::hasType($typeName)) {
                Type::addType($typeName, $typeClass);
                self::$registeredTypes[] = $typeName;
            }
        }
    }

    protected function createTestTable(string $tableName, string $columnName, string $columnType): void
    {
        $schemaManager = self::$connection->createSchemaManager();

        // Use the test schema for all tables
        $fullTableName = 'test.'.$tableName;

        // Drop table if it exists
        if ($schemaManager->tablesExist([$fullTableName])) {
            $schemaManager->dropTable($fullTableName);
        }

        // Create table with the specified column type
        $sql = \sprintf(
            'CREATE TABLE %s (id SERIAL PRIMARY KEY, %s %s)',
            $fullTableName,
            $columnName,
            $columnType
        );

        self::$connection->executeStatement($sql);
    }

    protected function dropTestTable(string $tableName): void
    {
        $schemaManager = self::$connection->createSchemaManager();

        if ($schemaManager->tablesExist([$tableName])) {
            $schemaManager->dropTable($tableName);
        }
    }
}
