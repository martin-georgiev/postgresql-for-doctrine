<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\EntityManager;
use Tests\Integration\MartinGeorgiev\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected EntityManager $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = new EntityManager($this->connection, $this->configuration);
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
        $this->entityManager->getConnection()->executeStatement('DROP SCHEMA IF EXISTS test CASCADE');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function executeDqlQuery(string $dql): array
    {
        $query = $this->entityManager->createQuery($dql);

        /* @var array<int, array<string, mixed>> */
        return $query->getArrayResult();
    }
}
