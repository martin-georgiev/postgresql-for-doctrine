<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktGeometryType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WktGeometryTypeTest extends TestCase
{
    #[DataProvider('provideValidGeometryTypes')]
    #[Test]
    public function can_create_from_string(string $typeString, WktGeometryType $wktGeometryType): void
    {
        $geometryType = WktGeometryType::from($typeString);

        self::assertSame($wktGeometryType, $geometryType);
        self::assertSame($typeString, $geometryType->value);
    }

    #[Test]
    public function throws_exception_for_invalid_type(): void
    {
        $this->expectException(\ValueError::class);

        WktGeometryType::from('INVALID');
    }

    #[DataProvider('provideValidGeometryTypes')]
    #[Test]
    public function returns_enum_for_valid_types(string $typeString, WktGeometryType $wktGeometryType): void
    {
        $result = WktGeometryType::tryFrom($typeString);

        self::assertSame($wktGeometryType, $result);
    }

    /**
     * @return array<string, array{string, WktGeometryType}>
     */
    public static function provideValidGeometryTypes(): array
    {
        return [
            'point' => ['POINT', WktGeometryType::POINT],
            'linestring' => ['LINESTRING', WktGeometryType::LINESTRING],
            'polygon' => ['POLYGON', WktGeometryType::POLYGON],
            'multipoint' => ['MULTIPOINT', WktGeometryType::MULTIPOINT],
            'multilinestring' => ['MULTILINESTRING', WktGeometryType::MULTILINESTRING],
            'multipolygon' => ['MULTIPOLYGON', WktGeometryType::MULTIPOLYGON],
            'geometrycollection' => ['GEOMETRYCOLLECTION', WktGeometryType::GEOMETRYCOLLECTION],
            'circularstring' => ['CIRCULARSTRING', WktGeometryType::CIRCULARSTRING],
            'compoundcurve' => ['COMPOUNDCURVE', WktGeometryType::COMPOUNDCURVE],
            'curvepolygon' => ['CURVEPOLYGON', WktGeometryType::CURVEPOLYGON],
            'multicurve' => ['MULTICURVE', WktGeometryType::MULTICURVE],
            'multisurface' => ['MULTISURFACE', WktGeometryType::MULTISURFACE],
            'triangle' => ['TRIANGLE', WktGeometryType::TRIANGLE],
            'tin' => ['TIN', WktGeometryType::TIN],
            'polyhedralsurface' => ['POLYHEDRALSURFACE', WktGeometryType::POLYHEDRALSURFACE],
        ];
    }

    #[Test]
    public function returns_null_for_invalid_types(): void
    {
        self::assertNull(WktGeometryType::tryFrom('INVALID_TYPE'));
        self::assertNull(WktGeometryType::tryFrom(''));
    }
}
