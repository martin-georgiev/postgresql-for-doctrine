<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHalfvecForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class HalfvecTypeTest extends VectorTypeTestCase
{
    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertIsArray($expected);
        $this->assertIsArray($actual);
        $this->assertCount(\count($expected), $actual);

        foreach ($expected as $i => $expectedValue) {
            $this->assertEqualsWithDelta($expectedValue, $actual[$i], 0.01, \sprintf('Element %d of %s round-trip failed', $i, $typeName));
        }
    }

    protected function getTypeName(): string
    {
        return 'halfvec';
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

    #[DataProvider('provideInvalidHalfvecValues')]
    #[Test]
    public function throws_exception_for_invalid_value_before_database_write(mixed $value): void
    {
        $this->expectException(InvalidHalfvecForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $value);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidHalfvecValues(): array
    {
        return [
            'non-array' => ['not an array'],
            'non-list array' => [['key' => 1.0]],
            'non-numeric element' => [[1.0, 'not a float', 2.0]],
            'infinite element' => [[1.0, INF, 2.0]],
        ];
    }
}
