<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

trait EnsurePostgresExtensionTrait
{
    protected function ensurePostgresExtensionInSchema(string $extensionName): void
    {
        try {
            $this->connection->executeStatement(
                \sprintf('CREATE EXTENSION IF NOT EXISTS "%s"', $extensionName)
            );
        } catch (\Throwable) {
            $this->markTestSkipped(\sprintf('%s extension is not available', $extensionName));
        }

        try {
            // Move the extension objects into the test schema to resolve types without relying on public
            $this->connection->executeStatement(
                \sprintf('ALTER EXTENSION "%s" SET SCHEMA %s', $extensionName, self::DATABASE_SCHEMA)
            );
        } catch (\Throwable) {
            // Fallback: if moving the extension is not possible, keep public in the search_path below
        }
    }
}
