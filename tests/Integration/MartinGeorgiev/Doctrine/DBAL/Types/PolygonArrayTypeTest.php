<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPolygonArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Polygon as PolygonValueObject;
use PHPUnit\Framework\Attributes\Test;

final class PolygonArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'polygon[]';
    }

    /**
     * @return array<string, array{array<int, PolygonValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single triangle' => [[
                PolygonValueObject::fromString('((0,0),(1,1),(2,0))'),
            ]],
            'multiple polygons' => [[
                PolygonValueObject::fromString('((0,0),(0,1),(1,1),(1,0))'),
                PolygonValueObject::fromString('((-1,-2),(-3,-4),(-5,-6))'),
            ]],
            'empty polygon array' => [[]],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidPolygonArrayItemForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, ['((0,0),(1,1),(2,0))']);
    }
}
