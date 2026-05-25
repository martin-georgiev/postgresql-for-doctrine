<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class CitextTypeTest extends ScalarTypeTestCase
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
        return 'citext';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(string $testValue): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $testValue);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple text' => ['Hello World'],
            'mixed case' => ['CaseInsensitive TEXT'],
            'empty string' => [''],
        ];
    }
}
