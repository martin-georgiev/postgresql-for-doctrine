<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SimplifyPolygonHull;
use PHPUnit\Framework\Attributes\Test;

class ST_SimplifyPolygonHullTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_SIMPLIFYPOLYGONHULL' => ST_SimplifyPolygonHull::class,
            'ST_AREA' => ST_Area::class,
            'ST_EQUALS' => ST_Equals::class,
        ];
    }

    #[Test]
    public function outer_hull_equals_original_for_simple_polygon(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_SIMPLIFYPOLYGONHULL(g.geometry1, 1.0), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'outer hull with ratio 1.0 should equal original simple polygon');
    }

    #[Test]
    public function outer_hull_preserves_area_for_simple_polygon(): void
    {
        $dql = 'SELECT ST_AREA(ST_SIMPLIFYPOLYGONHULL(g.geometry1, 1.0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result'], 'outer hull of 4x4 polygon should have area 16');
    }
}
