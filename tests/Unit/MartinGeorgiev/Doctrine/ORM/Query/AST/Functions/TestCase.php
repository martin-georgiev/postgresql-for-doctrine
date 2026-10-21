<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var string
     */
    protected const FIXTURES_DIRECTORY = __DIR__.'/../../../../../../fixtures/MartinGeorgiev/Doctrine/Entity';

    private Configuration $configuration;

    protected function setUp(): void
    {
        $configuration = ORMSetup::createAttributeMetadataConfiguration([static::FIXTURES_DIRECTORY], true);
        $configuration->setProxyDir(static::FIXTURES_DIRECTORY.'/Proxies');
        $configuration->setProxyNamespace('Fixtures\MartinGeorgiev\Doctrine\Entity\Proxy');
        $configuration->setAutoGenerateProxyClasses(true);

        $this->configuration = $configuration;

        $this->registerFunction();
    }

    private function registerFunction(): void
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

    /**
     * @return array<int|string, string>
     */
    abstract protected function getExpectedSqlStatements(): array;

    /**
     * @return array<int|string, string>
     */
    abstract protected function getDqlStatements(): array;

    #[Test]
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

    protected function assertSqlFromDql(string $expectedSql, string $dql, string $message = ''): void
    {
        $query = $this->buildEntityManager()->createQuery($dql);
        $this->assertSame($expectedSql, $query->getSQL(), $message);
    }

    protected function buildEntityManager(): EntityManager
    {
        return new EntityManager(DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true], $this->configuration), $this->configuration);
    }
}
