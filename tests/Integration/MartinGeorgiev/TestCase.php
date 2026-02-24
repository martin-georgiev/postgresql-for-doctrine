<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray;
use MartinGeorgiev\Doctrine\DBAL\Types\BooleanArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Cidr;
use MartinGeorgiev\Doctrine\DBAL\Types\CidrArray;
use MartinGeorgiev\Doctrine\DBAL\Types\DateRange;
use MartinGeorgiev\Doctrine\DBAL\Types\DoublePrecisionArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Geography;
use MartinGeorgiev\Doctrine\DBAL\Types\GeographyArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Geometry;
use MartinGeorgiev\Doctrine\DBAL\Types\GeometryArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Inet;
use MartinGeorgiev\Doctrine\DBAL\Types\InetArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Int4Range;
use MartinGeorgiev\Doctrine\DBAL\Types\Int8Range;
use MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Jsonb;
use MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Ltree;
use MartinGeorgiev\Doctrine\DBAL\Types\Macaddr;
use MartinGeorgiev\Doctrine\DBAL\Types\Macaddr8;
use MartinGeorgiev\Doctrine\DBAL\Types\Macaddr8Array;
use MartinGeorgiev\Doctrine\DBAL\Types\MacaddrArray;
use MartinGeorgiev\Doctrine\DBAL\Types\NumRange;
use MartinGeorgiev\Doctrine\DBAL\Types\Point;
use MartinGeorgiev\Doctrine\DBAL\Types\PointArray;
use MartinGeorgiev\Doctrine\DBAL\Types\RealArray;
use MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray;
use MartinGeorgiev\Doctrine\DBAL\Types\TextArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Tsquery;
use MartinGeorgiev\Doctrine\DBAL\Types\TsRange;
use MartinGeorgiev\Doctrine\DBAL\Types\TstzRange;
use MartinGeorgiev\Doctrine\DBAL\Types\Tsvector;
use MartinGeorgiev\Doctrine\DBAL\Types\UuidArray;
use MartinGeorgiev\Utils\PHPArrayToPostgresValueTransformer;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var string
     */
    protected const FIXTURES_DIRECTORY = __DIR__.'/../../../../../../fixtures/MartinGeorgiev/Doctrine/Entity';

    protected const FIXTURE_NAMESPACE = 'Fixtures\MartinGeorgiev\Doctrine\Entity';

    protected const DATABASE_SCHEMA = 'test';

    protected Configuration $configuration;

    protected Connection $connection;

    protected EntityManager $entityManager;

    protected static array $registeredTypes = [];

    protected function setUp(): void
    {
        // Set up a driver chain
        $mappingDriverChain = new MappingDriverChain();
        $attributeDriver = new AttributeDriver([self::FIXTURES_DIRECTORY]);
        $mappingDriverChain->addDriver($attributeDriver, self::FIXTURE_NAMESPACE);

        $configuration = ORMSetup::createAttributeMetadataConfiguration([], true);
        $configuration->setMetadataDriverImpl($mappingDriverChain);
        $configuration->setProxyDir(self::FIXTURES_DIRECTORY.'/Proxies');
        $configuration->setProxyNamespace(self::FIXTURE_NAMESPACE.'\Proxy');
        $configuration->setAutoGenerateProxyClasses(true);
        $this->setConfigurationCache($configuration);

        // Register the entity namespace for DQL short aliases
        $configuration->setEntityNamespaces([
            'Fixtures' => self::FIXTURE_NAMESPACE,
        ]);

        $this->configuration = $configuration;

        $this->setUpConnection();

        $this->createTestSchema();

        $this->registerCustomTypes();
        $this->registerCustomFunctions();

        $this->entityManager = new EntityManager($this->connection, $this->configuration);
    }

    protected function tearDown(): void
    {
        $this->connection->executeStatement('DROP SCHEMA IF EXISTS test CASCADE');
        $this->connection->close();
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

    protected function createTestSchema(): void
    {
        $this->connection->executeStatement(\sprintf('DROP SCHEMA IF EXISTS %s CASCADE', self::DATABASE_SCHEMA));
        $this->connection->executeStatement(\sprintf('CREATE SCHEMA %s', self::DATABASE_SCHEMA));

        // Ensure Ltree is available for hierarchy tree types
        // Ensure Ltree is available in the test schema
        try {
            // Ensure PostGIS is installed and, if possible, placed in the test schema
            $this->connection->executeStatement('CREATE EXTENSION IF NOT EXISTS ltree');
            // Move the extension objects into the test schema to resolve types without relying on public
            $this->connection->executeStatement(\sprintf('ALTER EXTENSION ltree SET SCHEMA %s', self::DATABASE_SCHEMA));
        } catch (\Throwable) {
            // Fallback: if moving the extension is not possible, keep public in the search_path below
        }

        // Ensure PostGIS is available for geometry/geography types
        // Ensure PostGIS is available in the test schema and as default search_path
        try {
            // Ensure PostGIS is installed and, if possible, placed in the test schema
            $this->connection->executeStatement('CREATE EXTENSION IF NOT EXISTS postgis');
            // Move the extension objects into the test schema to resolve types without relying on public
            $this->connection->executeStatement(\sprintf('ALTER EXTENSION postgis SET SCHEMA %s', self::DATABASE_SCHEMA));
        } catch (\Throwable) {
            // Fallback: if moving the extension is not possible, keep public in the search_path below
        }

        // Ensure our schema is first, but include public so extensions installed there resolve
        $this->connection->executeStatement(\sprintf('SET search_path TO %s, public', self::DATABASE_SCHEMA));
        // Stabilize timezone-dependent tests
        $this->connection->executeStatement("SET TIME ZONE 'UTC'");
    }

    protected function registerCustomTypes(): void
    {
        $typesMap = [
            'bigint[]' => BigIntArray::class,
            'bool[]' => BooleanArray::class,
            'cidr' => Cidr::class,
            'cidr[]' => CidrArray::class,
            'daterange' => DateRange::class,
            'double precision[]' => DoublePrecisionArray::class,
            'geography' => Geography::class,
            'geography[]' => GeographyArray::class,
            'geometry' => Geometry::class,
            'geometry[]' => GeometryArray::class,
            'inet' => Inet::class,
            'inet[]' => InetArray::class,
            'int4range' => Int4Range::class,
            'int8range' => Int8Range::class,
            'integer[]' => IntegerArray::class,
            'jsonb' => Jsonb::class,
            'jsonb[]' => JsonbArray::class,
            'ltree' => Ltree::class,
            'macaddr' => Macaddr::class,
            'macaddr8' => Macaddr8::class,
            'macaddr8[]' => Macaddr8Array::class,
            'macaddr[]' => MacaddrArray::class,
            'numrange' => NumRange::class,
            'point' => Point::class,
            'point[]' => PointArray::class,
            'real[]' => RealArray::class,
            'smallint[]' => SmallIntArray::class,
            'text[]' => TextArray::class,
            'tsquery' => Tsquery::class,
            'tsrange' => TsRange::class,
            'tstzrange' => TstzRange::class,
            'tsvector' => Tsvector::class,
            'uuid[]' => UuidArray::class,
        ];

        foreach ($typesMap as $typeName => $typeClass) {
            if (Type::hasType($typeName)) {
                Type::overrideType($typeName, $typeClass);
            } else {
                Type::addType($typeName, $typeClass);
            }

            self::$registeredTypes[] = $typeName;
        }
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

    protected function dropTestTableIfItExists(string $tableName): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        if ($schemaManager->tablesExist([$fullTableName])) {
            $schemaManager->dropTable($fullTableName);
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
        try {
            \assert(\is_string($value));

            return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($value);
        } catch (\Throwable) {
            return $value;
        }
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return array<int, array<string, mixed>>
     */
    protected function executeDqlQuery(string $dql, array $parameters = []): array
    {
        $query = $this->entityManager->createQuery($dql);

        foreach ($parameters as $key => $value) {
            if (\is_array($value)) {
                $postgresArray = PHPArrayToPostgresValueTransformer::transformToPostgresTextArray($value);
                $query->setParameter($key, $postgresArray);
            } else {
                $query->setParameter($key, $value);
            }
        }

        return $query->getArrayResult(); // @phpstan-ignore-line
    }

    /**
     * Get the PostgreSQL server version as a numeric value (e.g., 160003 for 16.0.3, 180000 for 18.0.0).
     */
    protected function getPostgresVersion(): int
    {
        $result = $this->connection->fetchOne('SHOW server_version_num');
        \assert(\is_string($result) || \is_int($result));

        return (int) $result;
    }

    /**
     * Get the PostGIS version as a numeric value (e.g., 30400 for 3.4.0, 30500 for 3.5.0).
     */
    protected function getPostgisVersion(): int
    {
        $result = $this->connection->fetchOne('SELECT postgis_lib_version()');
        \assert(\is_string($result));

        $parts = \explode('.', $result);
        $major = (int) ($parts[0] ?? 0);
        $minor = (int) ($parts[1] ?? 0);
        $patch = (int) ($parts[2] ?? 0);

        return $major * 10000 + $minor * 100 + $patch;
    }

    /**
     * Skip the test if PostgreSQL version is less than the required version.
     *
     * @param int $requiredVersion The minimum required PostgreSQL version (e.g., 180000 for PostgreSQL 18)
     * @param string $featureName Optional feature name to include in the skip message
     */
    protected function requirePostgresVersion(int $requiredVersion, string $featureName = ''): void
    {
        $this->requireVersion('PostgreSQL', $this->getPostgresVersion(), $requiredVersion, $featureName);
    }

    /**
     * Skip the test if PostGIS version is less than the required version.
     *
     * @param int $requiredVersion The minimum required PostGIS version (e.g., 30400 for PostGIS 3.4.0)
     * @param string $featureName Optional feature name to include in the skip message
     */
    protected function requirePostgisVersion(int $requiredVersion, string $featureName = ''): void
    {
        $this->requireVersion('PostGIS', $this->getPostgisVersion(), $requiredVersion, $featureName);
    }

    private function requireVersion(string $product, int $currentVersion, int $requiredVersion, string $featureName): void
    {
        if ($currentVersion >= $requiredVersion) {
            return;
        }

        $currentMajor = (int) ($currentVersion / 10000);
        $currentMinor = (int) (($currentVersion % 10000) / 100);
        $currentPatch = $currentVersion % 100;

        $requiredMajor = (int) ($requiredVersion / 10000);
        $requiredMinor = (int) (($requiredVersion % 10000) / 100);
        $requiredPatch = $requiredVersion % 100;

        $featureMessage = $featureName !== '' ? \sprintf(' (%s)', $featureName) : '';
        $message = \sprintf(
            'This test requires %s %d.%d.%d or later%s. Current version: %d.%d.%d',
            $product,
            $requiredMajor,
            $requiredMinor,
            $requiredPatch,
            $featureMessage,
            $currentMajor,
            $currentMinor,
            $currentPatch
        );

        $this->markTestSkipped($message);
    }
}
