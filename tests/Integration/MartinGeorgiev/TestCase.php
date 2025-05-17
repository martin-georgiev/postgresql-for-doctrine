<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
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
        $configuration = ORMSetup::createAttributeMetadataConfiguration([static::FIXTURES_DIRECTORY], true);
        $configuration->setProxyDir(static::FIXTURES_DIRECTORY.'/Proxies');
        $configuration->setProxyNamespace('Fixtures\MartinGeorgiev\Doctrine\Entity\Proxy');
        $configuration->setAutoGenerateProxyClasses(true);
        $this->setConfigurationCache($configuration);

        $this->configuration = $configuration;

        $this->setUpConnection();

        $this->registerCustomTypes();
        $this->registerCustomFunctions();
    }

    protected function tearDown(): void
    {
        if (isset($this->connection)) {
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
        $schemaManager = $this->connection->createSchemaManager();

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

        $this->connection->executeStatement($sql);
    }

    protected function dropTestTable(string $tableName): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        if ($schemaManager->tablesExist([$tableName])) {
            $schemaManager->dropTable($tableName);
        }
    }
}
