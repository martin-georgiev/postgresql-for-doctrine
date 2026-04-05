<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\Test;

abstract class VectorTypeTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureVectorExtension();
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    private function ensureVectorExtension(): void
    {
        try {
            $this->connection->executeStatement('CREATE EXTENSION IF NOT EXISTS vector');
            $this->connection->executeStatement(\sprintf('ALTER EXTENSION vector SET SCHEMA %s', self::DATABASE_SCHEMA));
        } catch (\Throwable) {
            $this->markTestSkipped('pgvector extension is not available');
        }
    }
}
