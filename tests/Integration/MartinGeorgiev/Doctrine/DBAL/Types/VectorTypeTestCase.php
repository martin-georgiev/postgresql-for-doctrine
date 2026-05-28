<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception\DriverException;
use PHPUnit\Framework\Attributes\Test;

abstract class VectorTypeTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensurePostgresExtensionInSchema('vector');
    }

    #[Test]
    public function roundtrips_null_value(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, null);
    }

    /**
     * Guards the dimension-aware `getSQLDeclaration()` contract end-to-end:
     * with `length` declared the column must enforce the dimension at insert time.
     * A regression that drops `(n)` from the declaration would silently let
     * any-width vectors through, so we prove the dimension constraint exists by
     * asserting PostgreSQL rejects a value whose dimension does not match.
     */
    #[Test]
    public function column_enforces_declared_dimension(): void
    {
        $declaration = $this->getFieldDeclaration();
        $this->assertArrayHasKey('length', $declaration, 'Vector tests must declare a length to exercise the dimension-aware SQL declaration');
        $this->assertIsInt($declaration['length']);
        $this->assertGreaterThan(0, $declaration['length']);
        /** @var positive-int $dimension */
        $dimension = $declaration['length'];

        $this->expectException(DriverException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip(
            $typeName,
            $columnType,
            $this->getValueWithMismatchedDimension($dimension),
        );
    }

    /**
     * A value whose serialized dimension does not match the declared length.
     *
     * @param positive-int $declaredDimension
     */
    abstract protected function getValueWithMismatchedDimension(int $declaredDimension): mixed;
}
