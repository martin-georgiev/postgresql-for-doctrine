<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Intersection;
use PHPUnit\Framework\Attributes\Test;

class ST_IntersectionTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_EQUALS' => ST_Equals::class,
            'ST_INTERSECTION' => ST_Intersection::class,
        ];
    }

    #[Test]
    public function returns_intersection_of_overlapping_polygons(): void
    {
        $dql = 'SELECT ST_AREA(ST_INTERSECTION(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 4';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result'], 'should calculate correct overlapping area between polygons');
    }

    #[Test]
    public function returns_empty_geometry_for_disjoint_geometries(): void
    {
        $dql = 'SELECT ST_AREA(ST_INTERSECTION(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result'], 'should return zero area for disjoint points');
    }

    #[Test]
    public function returns_geometry_for_identical_geometries(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_INTERSECTION(g.geometry1, g.geometry1), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'intersection of identical geometries should equal the original geometry');
    }
}
