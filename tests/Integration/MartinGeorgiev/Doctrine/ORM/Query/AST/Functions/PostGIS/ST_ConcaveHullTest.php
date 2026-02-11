<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ConcaveHull;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use PHPUnit\Framework\Attributes\Test;

class ST_ConcaveHullTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_CONCAVEHULL' => ST_ConcaveHull::class,
            'ST_AREA' => ST_Area::class,
            'ST_EQUALS' => ST_Equals::class,
        ];
    }

    #[Test]
    public function concave_hull_of_convex_polygon_equals_original(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_CONCAVEHULL(g.geometry1, 1.0), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'concave hull with ratio 1.0 should equal original convex polygon');
    }

    #[Test]
    public function concave_hull_preserves_area_for_convex_polygon(): void
    {
        $dql = 'SELECT ST_AREA(ST_CONCAVEHULL(g.geometry1, 1.0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result'], 'concave hull of 4x4 polygon should have area 16');
    }
}
