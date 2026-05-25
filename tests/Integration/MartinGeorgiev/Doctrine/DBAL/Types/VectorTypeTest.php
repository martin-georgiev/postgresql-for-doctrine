<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidVectorForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class VectorTypeTest extends VectorTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'vector';
    }

    protected function getFieldDeclaration(): array
    {
        return ['length' => 3];
    }

    /**
     * @param positive-int $declaredDimension
     *
     * @return list<float>
     */
    protected function getValueWithMismatchedDimension(int $declaredDimension): array
    {
        return \array_fill(0, $declaredDimension + 2, 1.0);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(array $testValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $testValue);
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

    #[DataProvider('provideInvalidVectorValues')]
    #[Test]
    public function rejects_invalid_value_before_database_write(mixed $value): void
    {
        $this->expectException(InvalidVectorForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $value);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidVectorValues(): array
    {
        return [
            'non-array' => ['not an array'],
            'non-list array' => [['key' => 1.0]],
            'non-numeric element' => [[1.0, 'not a float', 2.0]],
            'infinite element' => [[1.0, INF, 2.0]],
        ];
    }
}
