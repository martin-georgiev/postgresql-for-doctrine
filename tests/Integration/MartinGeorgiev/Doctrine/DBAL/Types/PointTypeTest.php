<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point as PointValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class PointTypeTest extends TestCase
{
    use PointAssertionTrait;

    protected function getTypeName(): string
    {
        return 'point';
    }

    protected function getPostgresTypeName(): string
    {
        return 'POINT';
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, null);
    }

    /**
     * Override to handle Point-specific coordinate precision comparison.
     */
    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        if (!$expected instanceof PointValueObject || !$actual instanceof PointValueObject) {
            throw new \InvalidArgumentException('PointTypeTest expects Point value objects.');
        }

        $this->assertPointEquals($expected, $actual, $typeName);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_point_values(string $testName, PointValueObject $pointValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $pointValueObject);
    }

    /**
     * @return array<string, array{string, PointValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple point' => ['simple point', new PointValueObject(1.23, 4.56)],
            'zero coordinates' => ['zero coordinates', new PointValueObject(0.0, 0.0)],
            'negative coordinates' => ['negative coordinates', new PointValueObject(-10.5, -20.75)],
            'high precision' => ['high precision', new PointValueObject(123.456789, -987.654321)],
            'integer coordinates' => ['integer coordinates', new PointValueObject(100, 200)],
        ];
    }
}
