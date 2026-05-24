<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\Test;

class CitextTypeTest extends ScalarTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        try {
            $this->connection->executeStatement('CREATE EXTENSION IF NOT EXISTS citext');
        } catch (\Throwable) {
            $this->markTestSkipped('citext extension is not available');
        }
    }

    protected function getTypeName(): string
    {
        return 'citext';
    }

    #[Test]
    public function can_handle_simple_citext_value(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, 'Hello World');
    }

    #[Test]
    public function can_handle_citext_with_mixed_case(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, 'CaseInsensitive TEXT');
    }
}
