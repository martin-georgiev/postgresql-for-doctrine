<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MaxDistance;
use PHPUnit\Framework\Attributes\Test;

class ST_MaxDistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_MAXDISTANCE' => ST_MaxDistance::class,
        ];
    }

    #[Test]
    public function returns_maximum_distance_between_points(): void
    {
        $dql = 'SELECT ST_MAXDISTANCE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.4142135623730951, $result[0]['result'], 0.0000000000000001, 'Maximum distance between POINT(0 0) and POINT(1 1) = √2');
    }

    #[Test]
    public function returns_zero_for_identical_geometries(): void
    {
        $dql = 'SELECT ST_MAXDISTANCE(g.geometry1, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function returns_maximum_distance_between_polygons(): void
    {
        $dql = 'SELECT ST_MAXDISTANCE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(4.242640687119285, $result[0]['result'], 0.0000000000000001, 'Maximum distance between polygon corners (0,0) to (3,3) = √18');
    }
}
