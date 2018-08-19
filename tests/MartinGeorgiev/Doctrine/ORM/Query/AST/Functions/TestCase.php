<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var Configuration
     */
    protected $configuration;

    protected function setUp()
    {
        $this->configuration = new Configuration();
        $this->configuration->setMetadataCacheImpl(new ArrayCache());
        $this->configuration->setQueryCacheImpl(new ArrayCache());
        $this->configuration->setProxyDir(__DIR__.'/../../../../Fixtures/Proxies');
        $this->configuration->setProxyNamespace('MartinGeorgiev\Tests\Doctrine\Fixtures\Proxies');
        $this->configuration->setAutoGenerateProxyClasses(true);
        $this->configuration->setMetadataDriverImpl($this->configuration->newDefaultAnnotationDriver(__DIR__.'/../../../../Fixtures/Entities'));

        $this->registerFunction();
    }

    private function registerFunction()
    {
        foreach ($this->getStringFunctions() as $dqlFunction => $functionClass) {
            $this->configuration->addCustomStringFunction($dqlFunction, $functionClass);
        }
    }

    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [];
    }

    /**
     * @return string|array
     */
    abstract protected function getExpectedSql();

    /**
     * @return string|array
     */
    abstract protected function getDql();

    /**
     * @test
     */
    public function dql_is_transformed_to_valid_sql()
    {
        $expectedSqls = $this->getExpectedSql();
        if (is_string($expectedSqls)) {
            $expectedSqls = [$expectedSqls];
        }
        $dqls = $this->getDql();
        if (is_string($dqls)) {
            $dqls = [$dqls];
        }
        if (count($expectedSqls) !== count($dqls)) {
            throw new \LogicException(sprintf('You need ot provide matching expected SQL for every DQL, currently there are %d SQL statements for %d DQL statements', count($expectedSqls), count($dqls)));
        }
        foreach ($expectedSqls as $key => $expectedSql) {
            $this->assertSqlFromDql($expectedSql, $dqls[$key], sprintf('Assertion failed for expected SQL statement "%s"', $expectedSql));
        }
    }

    /**
     * @param string $expectedSql
     * @param string $dql
     * @param string $message
     */
    private function assertSqlFromDql($expectedSql, $dql, $message = '')
    {
        $query = $this->buildEntityManager()->createQuery($dql);
        $this->assertEquals($expectedSql, $query->getSQL(), $message);
    }

    /**
     * @return EntityManager
     * @throws ORMException
     */
    private function buildEntityManager()
    {
        return EntityManager::create(['driver' => 'pdo_sqlite', 'memory' => true], $this->configuration);
    }
}
