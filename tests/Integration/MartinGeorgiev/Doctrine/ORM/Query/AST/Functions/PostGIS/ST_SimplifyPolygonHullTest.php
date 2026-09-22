<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Contains;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SimplifyPolygonHull;
use PHPUnit\Framework\Attributes\Test;

final class ST_SimplifyPolygonHullTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_CONTAINS' => ST_Contains::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_SIMPLIFYPOLYGONHULL' => ST_SimplifyPolygonHull::class,
        ];
    }

    #[Test]
    public function returns_the_polygon_hull_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_AREA(ST_SIMPLIFYPOLYGONHULL(ST_GEOMFROMTEXT('POLYGON((0 0,0 4,4 4,4 0,0 0))'), 1.0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }

    #[Test]
    public function returns_the_polygon_hull_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_AREA(ST_SIMPLIFYPOLYGONHULL(g.geometry1, 1.0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }

    #[Test]
    public function respects_the_is_outer_flag(): void
    {
        $dql = "SELECT ST_CONTAINS(g.geometry1, ST_SIMPLIFYPOLYGONHULL(g.geometry1, 0.5, 'false')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
