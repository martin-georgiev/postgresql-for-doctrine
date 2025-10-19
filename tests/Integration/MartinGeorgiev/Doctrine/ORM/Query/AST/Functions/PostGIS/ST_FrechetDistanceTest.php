<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_FrechetDistance;
use PHPUnit\Framework\Attributes\Test;

class ST_FrechetDistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_FRECHETDISTANCE' => ST_FrechetDistance::class,
        ];
    }

    #[Test]
    public function returns_frechet_distance_between_linestrings(): void
    {
        $dql = 'SELECT ST_FRECHETDISTANCE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(4.242640687119285, $result[0]['result'], 0.000000000000001, 'should return correct distance between disjoint linestrings');
    }

    #[Test]
    public function returns_zero_for_identical_geometries(): void
    {
        $dql = 'SELECT ST_FRECHETDISTANCE(g.geometry1, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function returns_frechet_distance_between_polygons(): void
    {
        $dql = 'SELECT ST_FRECHETDISTANCE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.4142135623730951, $result[0]['result'], 0.000000000000001, 'should return correct distance between overlapping polygons');
    }
}
