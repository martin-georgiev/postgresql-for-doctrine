<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_PointInsideCircle;

class ST_PointInsideCircleTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_POINTINSIDECIRCLE' => ST_PointInsideCircle::class,
        ];
    }

    public function test_function_with_point_inside_circle(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_PointInsideCircle(g.geometry1, 0, 0, 1000) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_PointInsideCircle(c0_.geometry1, 0, 0, 1000) AS sclr_0 FROM ContainsGeometries c0_'
        );
    }

    public function test_function_with_point_inside_circle_in_where_clause(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_PointInsideCircle(g.geometry1, 0, 0, 1000) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g WHERE ST_PointInsideCircle(g.geometry1, 0, 0, 1000) = TRUE',
            'SELECT ST_PointInsideCircle(c0_.geometry1, 0, 0, 1000) AS sclr_0 FROM ContainsGeometries c0_ WHERE ST_PointInsideCircle(c0_.geometry1, 0, 0, 1000) = TRUE'
        );
    }

    public function test_function_with_point_outside_circle(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_PointInsideCircle(g.geometry1, 0, 0, 1000) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g WHERE ST_PointInsideCircle(g.geometry1, 0, 0, 1000) = FALSE',
            'SELECT ST_PointInsideCircle(c0_.geometry1, 0, 0, 1000) AS sclr_0 FROM ContainsGeometries c0_ WHERE ST_PointInsideCircle(c0_.geometry1, 0, 0, 1000) = FALSE'
        );
    }
}
