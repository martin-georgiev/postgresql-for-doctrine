<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidSparsevecForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Sparsevec as SparsevecValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class SparsevecTypeTest extends VectorTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'sparsevec';
    }

    protected function getFieldDeclaration(): array
    {
        return ['length' => 5];
    }

    /**
     * @param positive-int $declaredDimension
     */
    protected function getValueWithMismatchedDimension(int $declaredDimension): SparsevecValueObject
    {
        $largerDimension = $declaredDimension + 2;

        return new SparsevecValueObject([1 => 1.0], $largerDimension);
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertInstanceOf(SparsevecValueObject::class, $expected);
        $this->assertInstanceOf(SparsevecValueObject::class, $actual);
        $this->assertSame((string) $expected, (string) $actual);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(SparsevecValueObject $sparsevecValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $sparsevecValueObject);
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

    #[DataProvider('provideInvalidSparsevecValues')]
    #[Test]
    public function rejects_invalid_value_before_database_write(mixed $value): void
    {
        $this->expectException(InvalidSparsevecForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $value);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidSparsevecValues(): array
    {
        return [
            'plain string' => ['not a value object'],
            'plain array' => [[1.0, 2.0, 3.0]],
            'integer' => [42],
        ];
    }
}
