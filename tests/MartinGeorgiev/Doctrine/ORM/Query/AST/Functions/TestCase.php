<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

abstract class TestCase extends BaseTestCase
{
    protected const FIXTURES_DIRECTORY = __DIR__.'/../../../../Fixtures';

    /**
     * @var Configuration
     */
    private $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = new Configuration();
        $this->configuration->setMetadataCache(new ArrayAdapter());
        $this->configuration->setQueryCache(new ArrayAdapter());
        $this->configuration->setProxyDir(static::FIXTURES_DIRECTORY.'/Proxies');
        $this->configuration->setProxyNamespace('Tests\MartinGeorgiev\Doctrine\Fixtures\Proxies');
        $this->configuration->setAutoGenerateProxyClasses(true);
        $this->configuration->setMetadataDriverImpl($this->configuration->newDefaultAnnotationDriver([static::FIXTURES_DIRECTORY.'/Entities']));

        $this->registerFunction();
    }

    private function registerFunction(): void
    {
        foreach ($this->getStringFunctions() as $dqlFunction => $functionClass) {
            $this->configuration->addCustomStringFunction($dqlFunction, $functionClass);
        }
    }

    /**
     * @return array<string, string>
     */
    protected function getStringFunctions(): array
    {
        return [];
    }

    /**
     * @return array<int, string>
     */
    abstract protected function getExpectedSqlStatements(): array;

    /**
     * @return array<int, string>
     */
    abstract protected function getDqlStatements(): array;

    /**
     * @test
     */
    public function dql_is_transformed_to_valid_sql(): void
    {
        $expectedSqls = $this->getExpectedSqlStatements();
        $dqls = $this->getDqlStatements();
        if (\count($expectedSqls) !== \count($dqls)) {
            throw new \LogicException(\sprintf('You need ot provide matching expected SQL for every DQL, currently there are %d SQL statements for %d DQL statements', \count($expectedSqls), \count($dqls)));
        }
        foreach ($expectedSqls as $key => $expectedSql) {
            $this->assertSqlFromDql($expectedSql, $dqls[$key], \sprintf('Assertion failed for expected SQL statement "%s"', $expectedSql));
        }
    }

    private function assertSqlFromDql(string $expectedSql, string $dql, string $message = ''): void
    {
        $query = $this->buildEntityManager()->createQuery($dql);
        $this->assertEquals($expectedSql, $query->getSQL(), $message);
    }

    /**
     * @throws ORMException
     */
    private function buildEntityManager(): EntityManager
    {
        return EntityManager::create(['driver' => 'pdo_sqlite', 'memory' => true], $this->configuration);
    }
}
