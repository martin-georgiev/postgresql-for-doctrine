<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Tests\Integration\MartinGeorgiev\TestCase;

abstract class TextTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTableForTextFixture();
        $this->insertTestDataForTextFixture();
    }

    protected function createTestTableForTextFixture(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS containstexts (
            id SERIAL PRIMARY KEY,
            text1 TEXT,
            text2 TEXT
        )';
        $this->entityManager->getConnection()->executeStatement($sql);
    }

    protected function insertTestDataForTextFixture(): void
    {
        $sql = "INSERT INTO containstexts (text1, text2) VALUES ('this is a test string', 'another test string')";
        $this->entityManager->getConnection()->executeStatement($sql);
    }
}
