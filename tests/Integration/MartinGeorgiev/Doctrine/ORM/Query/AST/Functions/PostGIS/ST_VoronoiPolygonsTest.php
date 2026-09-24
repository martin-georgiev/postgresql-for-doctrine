<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_VoronoiPolygons;
use PHPUnit\Framework\Attributes\Test;

final class ST_VoronoiPolygonsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMETRYTYPE' => ST_GeometryType::class,
            'ST_VORONOIPOLYGONS' => ST_VoronoiPolygons::class,
        ];
    }

    #[Test]
    public function returns_the_voronoi_polygons_from_an_entity_field(): void
    {
        $dql = 'SELECT ST_GEOMETRYTYPE(ST_VORONOIPOLYGONS(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_GeometryCollection', $result[0]['result']);
    }

    #[Test]
    public function returns_the_voronoi_polygons_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_GEOMETRYTYPE(ST_VORONOIPOLYGONS('LINESTRING(0 0, 1 1, 2 2)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_GeometryCollection', $result[0]['result']);
    }
}
