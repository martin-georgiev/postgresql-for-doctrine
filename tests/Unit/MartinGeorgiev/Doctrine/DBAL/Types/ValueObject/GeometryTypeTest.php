<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\GeometryType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GeometryTypeTest extends TestCase
{
    #[DataProvider('provideValidGeometryTypes')]
    #[Test]
    public function can_create_from_string(string $typeString, GeometryType $geometryType): void
    {
        $result = GeometryType::from($typeString);

        $this->assertSame($geometryType, $result);
        $this->assertSame($typeString, $result->value);
    }

    #[Test]
    public function throws_exception_for_invalid_type(): void
    {
        $this->expectException(\ValueError::class);

        GeometryType::from('INVALID');
    }

    #[DataProvider('provideValidGeometryTypes')]
    #[Test]
    public function returns_enum_for_valid_types(string $typeString, GeometryType $geometryType): void
    {
        $result = GeometryType::tryFrom($typeString);

        $this->assertSame($geometryType, $result);
    }

    /**
     * @return array<string, array{string, GeometryType}>
     */
    public static function provideValidGeometryTypes(): array
    {
        return [
            'point' => ['POINT', GeometryType::POINT],
            'linestring' => ['LINESTRING', GeometryType::LINESTRING],
            'polygon' => ['POLYGON', GeometryType::POLYGON],
            'multipoint' => ['MULTIPOINT', GeometryType::MULTIPOINT],
            'multilinestring' => ['MULTILINESTRING', GeometryType::MULTILINESTRING],
            'multipolygon' => ['MULTIPOLYGON', GeometryType::MULTIPOLYGON],
            'geometrycollection' => ['GEOMETRYCOLLECTION', GeometryType::GEOMETRYCOLLECTION],
            'circularstring' => ['CIRCULARSTRING', GeometryType::CIRCULARSTRING],
            'compoundcurve' => ['COMPOUNDCURVE', GeometryType::COMPOUNDCURVE],
            'curvepolygon' => ['CURVEPOLYGON', GeometryType::CURVEPOLYGON],
            'multicurve' => ['MULTICURVE', GeometryType::MULTICURVE],
            'multisurface' => ['MULTISURFACE', GeometryType::MULTISURFACE],
            'triangle' => ['TRIANGLE', GeometryType::TRIANGLE],
            'tin' => ['TIN', GeometryType::TIN],
            'polyhedralsurface' => ['POLYHEDRALSURFACE', GeometryType::POLYHEDRALSURFACE],
        ];
    }

    #[Test]
    public function returns_null_for_invalid_types(): void
    {
        $this->assertNull(GeometryType::tryFrom('INVALID_TYPE'));
        $this->assertNull(GeometryType::tryFrom(''));
    }
}
