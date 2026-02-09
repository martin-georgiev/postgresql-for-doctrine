<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TextTestCase;

abstract class TestCase extends TextTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureFuzzystrmatchExtension();
    }

    private function ensureFuzzystrmatchExtension(): void
    {
        try {
            $this->connection->executeStatement('CREATE EXTENSION IF NOT EXISTS fuzzystrmatch');
        } catch (\Exception) {
            $this->markTestSkipped('fuzzystrmatch extension is not available');
        }
    }
}
