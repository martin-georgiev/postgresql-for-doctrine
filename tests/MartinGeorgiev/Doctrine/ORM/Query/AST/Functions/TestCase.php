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
     * @return string
     */
    abstract protected function getExpectedSql();

    /**
     * @return string
     */
    abstract protected function getDql();

    /**
     * @test
     */
    public function dql_is_transformed_to_valid_sql()
    {
        $this->assertSqlFromDql($this->getExpectedSql(), $this->getDql());
    }

    /**
     * @param string $expectedSql
     * @param string $dql
     * @param array $paramsForDql
     */
    private function assertSqlFromDql($expectedSql, $dql, array $paramsForDql = [])
    {
        $query = $this->buildEntityManager()->createQuery($dql);
        foreach ($paramsForDql as $paramName => $paramValue) {
            $query->setParameter($paramName, $paramValue);
        }
        $this->assertEquals($expectedSql, $query->getSQL());
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
