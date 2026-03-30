<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Polygon as PolygonValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class PolygonTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'polygon';
    }

    protected function getPostgresTypeName(): string
    {
        return 'POLYGON';
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertInstanceOf(PolygonValueObject::class, $expected);
        $this->assertInstanceOf(PolygonValueObject::class, $actual);
        $this->assertSame($expected->__toString(), $actual->__toString(), \sprintf('Type %s round-trip failed', $typeName));
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_polygon_values(string $testName, PolygonValueObject $polygonValueObject): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $polygonValueObject);
    }

    /**
     * @return array<string, array{string, PolygonValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'triangle' => ['triangle', new PolygonValueObject('((0,0),(1,1),(2,0))')],
            'square' => ['square', new PolygonValueObject('((0,0),(0,1),(1,1),(1,0))')],
            'polygon with floats' => ['polygon with floats', new PolygonValueObject('((1.5,2.5),(3.5,4.5),(5.5,6.5))')],
            'polygon with negative coordinates' => ['polygon with negative coordinates', new PolygonValueObject('((-1,-2),(-3,-4),(-5,-6))')],
        ];
    }
}
