<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NumGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_VoronoiPolygons;
use PHPUnit\Framework\Attributes\Test;

final class ST_VoronoiPolygonsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMETRYTYPE' => ST_GeometryType::class,
            'ST_NUMGEOMETRIES' => ST_NumGeometries::class,
            'ST_VORONOIPOLYGONS' => ST_VoronoiPolygons::class,
        ];
    }

    #[Test]
    public function returns_geometry_collection(): void
    {
        $dql = 'SELECT ST_GEOMETRYTYPE(ST_VORONOIPOLYGONS(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_GeometryCollection', $result[0]['result']);
    }

    #[Test]
    public function returns_voronoi_polygons_for_linestring_vertices(): void
    {
        $dql = 'SELECT ST_NUMGEOMETRIES(ST_VORONOIPOLYGONS(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3, $result[0]['result']);
    }
}
