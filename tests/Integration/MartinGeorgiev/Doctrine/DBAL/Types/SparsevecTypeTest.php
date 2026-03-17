<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Sparsevec as SparsevecValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class SparsevecTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureVectorExtension();
    }

    protected function getTypeName(): string
    {
        return 'sparsevec';
    }

    protected function getPostgresTypeName(): string
    {
        return 'SPARSEVEC(5)';
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertInstanceOf(SparsevecValueObject::class, $expected);
        $this->assertInstanceOf(SparsevecValueObject::class, $actual);
        $this->assertSame((string) $expected, (string) $actual);
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(SparsevecValueObject $sparsevecValueObject): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $sparsevecValueObject);
    }

    /**
     * @return array<string, array{SparsevecValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single element' => [new SparsevecValueObject([2 => 0.5], 5)],
            'multiple elements' => [new SparsevecValueObject([1 => 1.5, 3 => 2.0], 5)],
            'empty elements' => [new SparsevecValueObject([], 5)],
            'negative value' => [new SparsevecValueObject([1 => -0.5], 5)],
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
