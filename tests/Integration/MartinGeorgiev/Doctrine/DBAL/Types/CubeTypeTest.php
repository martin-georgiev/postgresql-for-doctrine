<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCubeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Cube as CubeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class CubeTypeTest extends ScalarTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensurePostgresExtensionInSchema('cube');
    }

    protected function getTypeName(): string
    {
        return 'cube';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(CubeValueObject $cubeValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $cubeValueObject);
    }

    /**
     * @return array<string, array{CubeValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'point' => [CubeValueObject::point(1.0, 2.0, 3.0)],
            'box' => [new CubeValueObject([1.0, 2.0, 3.0], [4.0, 5.0, 6.0])],
            'one-dimensional point' => [CubeValueObject::point(42.0)],
            'negative coordinates' => [new CubeValueObject([-1.5, -2.5], [-0.5, -0.25])],
            'reversed corner order' => [new CubeValueObject([4.0, 5.0, 6.0], [1.0, 2.0, 3.0])],
            'high precision coordinates' => [CubeValueObject::point(0.12345678901234568, 1.0)],
            'exponent scale coordinates' => [CubeValueObject::point(1.0E+300, 1.0E-10)],
        ];
    }

    #[Test]
    public function normalizes_zero_volume_box_to_point(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, new CubeValueObject([1.0, 2.0], [1.0, 2.0]));
    }

    #[Test]
    public function rejects_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidCubeForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, '(1, 2, 3)');
    }
}
