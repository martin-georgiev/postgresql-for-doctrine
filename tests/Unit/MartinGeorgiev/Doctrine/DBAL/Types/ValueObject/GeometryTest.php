<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidGeometryException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Geometry;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktGeometryType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GeometryTest extends TestCase
{
    #[DataProvider('provideValidWkt')]
    #[Test]
    public function can_create_from_wkt(string $wkt, string $expectedType, ?int $expectedSrid): void
    {
        $geometry = Geometry::fromWkt($wkt);

        self::assertSame(WktGeometryType::from($expectedType), $geometry->getGeometryType());
        self::assertSame($expectedSrid, $geometry->getSrid());
        self::assertSame($wkt, $geometry->getWkt());
        self::assertSame($wkt, (string) $geometry);
    }

    /**
     * @return array<string, array{string, string, int|null}>
     */
    public static function provideValidWkt(): array
    {
        return [
            'point' => ['POINT(1 2)', 'POINT', null],
            'point with srid' => ['SRID=4326;POINT(-122.4194 37.7749)', 'POINT', 4326],
            'linestring' => ['LINESTRING(0 0, 1 1, 2 2)', 'LINESTRING', null],
            'polygon' => ['POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))', 'POLYGON', null],
        ];
    }

    #[DataProvider('provideInvalidWkt')]
    #[Test]
    public function throws_exception_for_invalid_wkt(string $invalidWkt): void
    {
        $this->expectException(InvalidGeometryException::class);
        Geometry::fromWkt($invalidWkt);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidWkt(): array
    {
        return [
            'empty' => [''],
            'missing semicolon after srid' => ['SRID=4326POINT(1 2)'],
            'invalid srid' => ['SRID=abc;POINT(1 2)'],
            'invalid body' => ['POINT()'],
            'invalid format' => ['INVALID_WKT'],
            'unsupported geometry type' => ['UNSUPPORTED(1 2)'],
        ];
    }
}

