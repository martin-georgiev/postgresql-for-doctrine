<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Distance;
use PHPUnit\Framework\Attributes\Test;

class ST_DistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_DISTANCE' => ST_Distance::class,
        ];
    }

    #[Test]
    public function returns_distance_between_points(): void
    {
        $dql = 'SELECT ST_DISTANCE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.4142135623730951, $result[0]['result'], 0.0000000000000001, 'Distance between POINT(0 0) and POINT(1 1) = √2');
    }

    #[Test]
    public function returns_zero_for_identical_geometries(): void
    {
        $dql = 'SELECT ST_DISTANCE(g.geometry1, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_for_distance_between_overlapping_polygons(): void
    {
        $dql = 'SELECT ST_DISTANCE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }
}
