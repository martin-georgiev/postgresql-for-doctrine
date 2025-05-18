<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
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
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var string
     */
    protected const FIXTURES_DIRECTORY = __DIR__.'/../../../../../../fixtures/MartinGeorgiev/Doctrine/Entity';

    protected Configuration $configuration;

    protected Connection $connection;

    protected static array $registeredTypes = [];

    protected function setUp(): void
    {
        // Set up a driver chain
        $mappingDriverChain = new MappingDriverChain();
        $entityNamespace = 'Fixtures\MartinGeorgiev\Doctrine\Entity';
        $entityDir = __DIR__.'/../../../../../../fixtures/MartinGeorgiev/Doctrine/Entity';
        $attributeDriver = new AttributeDriver([$entityDir]);
        $mappingDriverChain->addDriver($attributeDriver, $entityNamespace);

        $configuration = ORMSetup::createAttributeMetadataConfiguration([], true);
        $configuration->setMetadataDriverImpl($mappingDriverChain);
        $configuration->setProxyDir($entityDir.'/Proxies');
        $configuration->setProxyNamespace('Fixtures\MartinGeorgiev\Doctrine\Entity\Proxy');
        $configuration->setAutoGenerateProxyClasses(true);
        $this->setConfigurationCache($configuration);

        // Register the entity namespace for DQL short aliases
        $configuration->setEntityNamespaces([
            'Fixtures' => $entityNamespace,
        ]);

        $this->configuration = $configuration;

        $this->setUpConnection();

        $this->registerCustomTypes();
        $this->registerCustomFunctions();

        // Create test schema and insert test data
        $this->createTestSchema();
        $this->insertTestData();
    }

    protected function createTestSchema(): void
    {
        $this->connection->executeStatement('DROP SCHEMA IF EXISTS public CASCADE');
        $this->connection->executeStatement('CREATE SCHEMA public');
        $this->connection->executeStatement('
            CREATE TABLE array_test (
                id SERIAL PRIMARY KEY,
                text_array TEXT[],
                int_array INTEGER[],
                bool_array BOOLEAN[]
            )
        ');
    }

    protected function insertTestData(): void
    {
        $this->connection->executeStatement("
            INSERT INTO array_test (text_array, int_array, bool_array) VALUES
            (ARRAY['apple', 'banana', 'orange'], ARRAY[1, 2, 3], ARRAY[true, false, true]),
            (ARRAY['grape', 'apple'], ARRAY[4, 1], ARRAY[false, true]),
            (ARRAY['banana', 'orange', 'kiwi', 'mango'], ARRAY[2, 3, 7, 8], ARRAY[true, true, false, true])
        ");
    }

    protected function tearDown(): void
    {
        if (isset($this->connection)) {
            $this->connection->executeStatement('DROP SCHEMA IF EXISTS public CASCADE');
            $this->connection->close();
        }
    }

    private function setConfigurationCache(Configuration $configuration): void
    {
        $symfonyArrayAdapterClass = '\\'.ArrayAdapter::class;
        // @phpstan-ignore-next-line
        $useDbalV3 = \class_exists($symfonyArrayAdapterClass) && \method_exists($configuration, 'setMetadataCache') && \method_exists($configuration, 'setQueryCache');
        if ($useDbalV3) {
            // @phpstan-ignore-next-line
            $configuration->setMetadataCache(new $symfonyArrayAdapterClass());
            // @phpstan-ignore-next-line
            $configuration->setQueryCache(new $symfonyArrayAdapterClass());

            return;
        }

        $doctrineArrayCacheClass = '\Doctrine\Common\Cache\ArrayCache';
        // @phpstan-ignore-next-line
        $useDbalV2 = \class_exists($doctrineArrayCacheClass) && \method_exists($configuration, 'setMetadataCacheImpl') && \method_exists($configuration, 'setQueryCacheImpl');
        if ($useDbalV2) {
            // @phpstan-ignore-next-line
            $configuration->setMetadataCacheImpl(new $doctrineArrayCacheClass());
            // @phpstan-ignore-next-line
            $configuration->setQueryCacheImpl(new $doctrineArrayCacheClass());

            return;
        }

        throw new \RuntimeException('No known compatible version of doctrine/dbal found. Please report an issue on GitHub.');
    }

    private function registerCustomFunctions(): void
    {
        /**
         * @var class-string<FunctionNode> $functionClassName
         */
        foreach ($this->getStringFunctions() as $dqlFunction => $functionClassName) {
            $this->configuration->addCustomStringFunction($dqlFunction, $functionClassName);
        }
    }

    /**
     * @return array<string, string>
     */
    protected function getStringFunctions(): array
    {
        return [];
    }

    protected function setUpConnection(): void
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

        $this->connection = DriverManager::getConnection($connectionParams);

        // Create test schema if it doesn't exist
        $this->connection->executeStatement('CREATE SCHEMA IF NOT EXISTS test');
    }

    protected static function registerCustomTypes(): void
    {
        $typesMap = [
            'bigint[]' => BigIntArray::class,
            'boolean[]' => BooleanArray::class,
            'bool[]' => BooleanArray::class,
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
        $schemaManager = $this->connection->createSchemaManager();

        // Use the test schema for all tables
        $fullTableName = 'test.'.$tableName;

        // Ensure schema exists
        $this->connection->executeStatement('CREATE SCHEMA IF NOT EXISTS test');

        // Drop table if it already exists
        if ($schemaManager->tablesExist([$fullTableName])) {
            $schemaManager->dropTable($fullTableName);
        }

        // Create table with the specified column type, quoting identifiers
        $sql = \sprintf(
            'CREATE TABLE "%s"."%s" (id SERIAL PRIMARY KEY, "%s" %s)',
            'test',
            $tableName,
            $columnName,
            $columnType
        );

        $this->connection->executeStatement($sql);
    }

    protected function dropTestTable(string $tableName): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        if ($schemaManager->tablesExist([$tableName])) {
            $schemaManager->dropTable($tableName);
        }
    }

    /**
     * Transforms a PostgreSQL array string to a PHP array if needed.
     *
     * @param mixed $value The value to transform
     *
     * @return mixed The transformed value
     */
    protected function transformPostgresArray(mixed $value): mixed
    {
        if (\is_string($value) && \str_starts_with($value, '{') && \str_ends_with($value, '}')) {
            return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($value);
        }

        return $value;
    }
}
