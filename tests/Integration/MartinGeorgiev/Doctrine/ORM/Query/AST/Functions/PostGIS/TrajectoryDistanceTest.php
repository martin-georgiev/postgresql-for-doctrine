<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MakeLine;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\TrajectoryDistance;
use PHPUnit\Framework\Attributes\Test;

final class TrajectoryDistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_MAKELINE' => ST_MakeLine::class,
            'TRAJECTORY_DISTANCE' => TrajectoryDistance::class,
        ];
    }

    #[Test]
    public function returns_the_trajectory_distance_between_wkt_literals(): void
    {
        $dql = "SELECT TRAJECTORY_DISTANCE(ST_GEOMFROMTEXT('LINESTRING M(0 0 1,1 1 2)'), ST_GEOMFROMTEXT('LINESTRING M(2 2 1,3 3 2)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }

    #[Test]
    public function returns_the_trajectory_distance_between_entity_fields(): void
    {
        $dql = "SELECT TRAJECTORY_DISTANCE(ST_MAKELINE(g.geometry1, g.geometry2), ST_GEOMFROMTEXT('LINESTRING M(2 2 5,3 3 10)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 12";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }
}
