<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector;

use Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TextTestCase;

abstract class TestCase extends TextTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureVectorExtension();
    }

    private function ensureVectorExtension(): void
    {
        try {
            $this->connection->executeStatement('CREATE EXTENSION IF NOT EXISTS vector');
        } catch (\Exception) {
            $this->markTestSkipped('pgvector extension is not available');
        }
    }
}
