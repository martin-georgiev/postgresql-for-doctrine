<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ConvexHull;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use PHPUnit\Framework\Attributes\Test;

class ST_ConvexHullTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_CONVEXHULL' => ST_ConvexHull::class,
            'ST_EQUALS' => ST_Equals::class,
            'ST_LENGTH' => ST_Length::class,
        ];
    }

    #[Test]
    public function returns_convex_hull_of_point(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_CONVEXHULL(g.geometry1), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'convex hull of a point should be the point itself');
    }

    #[Test]
    public function returns_convex_hull_of_polygon(): void
    {
        $dql = 'SELECT ST_AREA(ST_CONVEXHULL(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result'], 'convex hull should preserve area for rectangular polygons');
    }

    #[Test]
    public function returns_convex_hull_of_linestring(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_CONVEXHULL(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.8284271247461903, $result[0]['result'], 0.0000000000000001, 'convex hull should preserve length for collinear linestring');
    }
}
