<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Contains;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SimplifyPolygonHull;
use PHPUnit\Framework\Attributes\Test;

final class ST_SimplifyPolygonHullTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_SIMPLIFYPOLYGONHULL' => ST_SimplifyPolygonHull::class,
            'ST_CONTAINS' => ST_Contains::class,
            'ST_EQUALS' => ST_Equals::class,
        ];
    }

    #[Test]
    public function returns_the_simplified_polygon_hull_from_entity_fields(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_SIMPLIFYPOLYGONHULL(g.geometry1, 1.0), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_the_simplified_polygon_hull_with_is_outer(): void
    {
        $dql = "SELECT ST_CONTAINS(g.geometry1, ST_SIMPLIFYPOLYGONHULL(g.geometry1, 0.5, 'false')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_the_simplified_polygon_hull_from_a_literal_operand(): void
    {
        $dql = "SELECT ST_EQUALS(ST_SIMPLIFYPOLYGONHULL(g.geometry1, 1.0), 'SRID=4326;POLYGON((0 0, 0 4, 4 4, 4 0, 0 0))') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
