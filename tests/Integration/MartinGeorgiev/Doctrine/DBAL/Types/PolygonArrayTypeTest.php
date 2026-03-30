<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Polygon as PolygonValueObject;

class PolygonArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'polygon[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'POLYGON[]';
    }

    /**
     * @return array<string, array{string, array<int, PolygonValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single triangle' => ['single triangle', [
                new PolygonValueObject('((0,0),(1,1),(2,0))'),
            ]],
            'multiple polygons' => ['multiple polygons', [
                new PolygonValueObject('((0,0),(0,1),(1,1),(1,0))'),
                new PolygonValueObject('((-1,-2),(-3,-4),(-5,-6))'),
            ]],
        ];
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertIsArray($expected);
        $this->assertIsArray($actual);
        $this->assertCount(\count($expected), $actual, \sprintf('Array count mismatch for type %s', $typeName));

        foreach ($expected as $index => $expectedItem) {
            $this->assertInstanceOf(PolygonValueObject::class, $expectedItem);
            $this->assertInstanceOf(PolygonValueObject::class, $actual[$index]);
            $this->assertSame($expectedItem->__toString(), $actual[$index]->__toString());
        }
    }
}
