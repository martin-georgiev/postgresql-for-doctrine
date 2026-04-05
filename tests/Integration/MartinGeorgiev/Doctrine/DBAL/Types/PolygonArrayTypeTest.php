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
                PolygonValueObject::fromString('((0,0),(1,1),(2,0))'),
            ]],
            'multiple polygons' => ['multiple polygons', [
                PolygonValueObject::fromString('((0,0),(0,1),(1,1),(1,0))'),
                PolygonValueObject::fromString('((-1,-2),(-3,-4),(-5,-6))'),
            ]],
        ];
    }
}
