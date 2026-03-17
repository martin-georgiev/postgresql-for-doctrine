<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class VectorTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureVectorExtension();
    }

    protected function getTypeName(): string
    {
        return 'vector';
    }

    protected function getPostgresTypeName(): string
    {
        return 'VECTOR(3)';
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(array $testValue): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $testValue);
    }

    /**
     * @return array<string, array{list<float>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'integer values' => [[1.0, 2.0, 3.0]],
            'float values' => [[0.1, 0.2, 0.3]],
            'negative values' => [[-1.5, 0.0, 1.5]],
            'zero vector' => [[0.0, 0.0, 0.0]],
        ];
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
