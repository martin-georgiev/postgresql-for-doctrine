<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_FrechetDistance;
use PHPUnit\Framework\Attributes\Test;

final class ST_FrechetDistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_FRECHETDISTANCE' => ST_FrechetDistance::class,
        ];
    }

    #[Test]
    public function returns_the_frechet_distance_from_entity_fields(): void
    {
        $dql = 'SELECT ST_FRECHETDISTANCE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(4.242640687119285, $result[0]['result']);
    }

    #[Test]
    public function returns_frechet_distance_between_disjoint_linestrings_with_densify_frac_parameter(): void
    {
        $dql = 'SELECT ST_FRECHETDISTANCE(g.geometry1, g.geometry2, 0.85) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(4.242640687119285, $result[0]['result']);
    }

    #[Test]
    public function returns_the_frechet_distance_from_wkt_literals(): void
    {
        $dql = "SELECT ST_FRECHETDISTANCE('LINESTRING(0 0, 1 1, 2 2)', 'LINESTRING(3 3, 4 4, 5 5)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(4.242640687119285, $result[0]['result']);
    }
}
