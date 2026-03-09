<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm;

use Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TextTestCase;

abstract class TestCase extends TextTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->ensurePgTrgmExtension();
    }

    private function ensurePgTrgmExtension(): void
    {
        try {
            $this->connection->executeStatement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        } catch (\Exception) {
            $this->markTestSkipped('pg_trgm extension is not available');
        }
    }
}
