<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

class CitextArrayTypeTest extends ArrayTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        try {
            $this->connection->executeStatement('CREATE EXTENSION IF NOT EXISTS citext');
            $this->connection->executeStatement(\sprintf('ALTER EXTENSION citext SET SCHEMA %s', self::DATABASE_SCHEMA));
        } catch (\Throwable) {
            $this->markTestSkipped('citext extension is not available');
        }
    }

    protected function getTypeName(): string
    {
        return 'citext[]';
    }

    public static function provideValidTransformations(): array
    {
        return [
            'simple string array' => [['foo', 'bar', 'baz']],
            'mixed case array' => [['Hello', 'WORLD', 'CamelCase']],
            'array with special chars' => [['café', 'naïve']],
            'array with null item' => [[null, 'hello']],
            'array with empty string' => [['', 'hello']],
        ];
    }
}
