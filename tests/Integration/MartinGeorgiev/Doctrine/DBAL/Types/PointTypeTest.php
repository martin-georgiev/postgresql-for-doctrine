<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point as PointValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class PointTypeTest extends TestCase
{
    use PointAssertionTrait;

    protected function getTypeName(): string
    {
        return 'point';
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
    public function can_handle_point_values(PointValueObject $pointValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $pointValueObject);
    }

    /**
     * @return array<string, array{PointValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple point' => [new PointValueObject(1.23, 4.56)],
            'zero coordinates' => [new PointValueObject(0.0, 0.0)],
            'negative coordinates' => [new PointValueObject(-10.5, -20.75)],
            'high precision' => [new PointValueObject(123.456789, -987.654321)],
            'integer coordinates' => [new PointValueObject(100, 200)],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidPointForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, '(1.0,2.0)');
    }
}
