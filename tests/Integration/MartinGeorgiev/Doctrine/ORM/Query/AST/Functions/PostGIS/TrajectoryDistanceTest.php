<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\TrajectoryDistance;
use PHPUnit\Framework\Attributes\Test;

final class TrajectoryDistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TRAJECTORY_DISTANCE' => TrajectoryDistance::class,
        ];
    }

    #[Test]
    public function returns_the_trajectory_distance_from_wkt_literals(): void
    {
        $dql = "SELECT TRAJECTORY_DISTANCE('LINESTRING M(0 0 1, 1 1 2)', 'LINESTRING M(2 2 1, 3 3 2)') as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['distance']);
        $this->assertGreaterThanOrEqual(0, $result[0]['distance']);
    }
}
