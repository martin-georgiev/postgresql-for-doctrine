<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NumGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_VoronoiPolygons;
use PHPUnit\Framework\Attributes\Test;

final class ST_VoronoiPolygonsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_NUMGEOMETRIES' => ST_NumGeometries::class,
            'ST_VORONOIPOLYGONS' => ST_VoronoiPolygons::class,
        ];
    }

    #[Test]
    public function returns_the_voronoi_polygons_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_NUMGEOMETRIES(ST_VORONOIPOLYGONS(ST_GEOMFROMTEXT('MULTIPOINT((0 0),(1 1),(2 2))'))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3, $result[0]['result']);
    }

    #[Test]
    public function returns_the_voronoi_polygons_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_NUMGEOMETRIES(ST_VORONOIPOLYGONS(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3, $result[0]['result']);
    }
}
