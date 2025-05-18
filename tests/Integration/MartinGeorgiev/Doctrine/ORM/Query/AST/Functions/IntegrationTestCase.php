<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Tests\Integration\MartinGeorgiev\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected EntityManager $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = new EntityManager($this->connection, $this->configuration);

        self::createTestSchema($this->connection);
        self::insertTestData($this->connection);
    }

    protected static function createTestSchema(Connection $connection): void
    {
        $connection->executeStatement('DROP SCHEMA IF EXISTS public CASCADE');
        $connection->executeStatement('CREATE SCHEMA public');
        $connection->executeStatement('
            CREATE TABLE array_test (
                id SERIAL PRIMARY KEY,
                text_array TEXT[],
                int_array INTEGER[],
                bool_array BOOLEAN[]
            )
        ');
    }

    protected static function insertTestData(Connection $connection): void
    {
        $connection->executeStatement("
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
        $result = $this->entityManager->createQuery($dql)->getResult();
        \assert(\is_array($result));

        // Ensure the result matches the expected return type array<int, array<string, mixed>>
        $normalizedResult = [];
        foreach ($result as $row) {
            if (\is_array($row)) {
                // Make sure all keys in the row are strings
                $normalizedRow = [];
                foreach ($row as $key => $value) {
                    $stringKey = \is_string($key) ? $key : 'item_'.$key;
                    $normalizedRow[$stringKey] = $value;
                }

                $normalizedResult[] = $normalizedRow;
            } else {
                // Handle scalar or object results by wrapping them
                $normalizedResult[] = ['value' => $row];
            }
        }

        return $normalizedResult;
    }
}
