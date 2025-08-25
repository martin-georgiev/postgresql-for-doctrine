<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TrajectoryDistance;
use PHPUnit\Framework\Attributes\Test;

class TrajectoryDistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TRAJECTORY_DISTANCE' => TrajectoryDistance::class,
        ];
    }

    #[Test]
    public function trajectory_distance_with_measured_geometries(): void
    {
        // Test with linestrings that have measure values (trajectories)
        $dql = "SELECT TRAJECTORY_DISTANCE('LINESTRING M(0 0 1, 1 1 2)', 'LINESTRING M(2 2 1, 3 3 2)') as distance 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['distance']);
        $this->assertGreaterThanOrEqual(0, $result[0]['distance']);
    }

    #[Test]
    public function trajectory_distance_with_identical_trajectories(): void
    {
        $dql = "SELECT TRAJECTORY_DISTANCE('LINESTRING M(0 0 1, 1 1 2)', 'LINESTRING M(0 0 1, 1 1 2)') as distance 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['distance']);
    }

    #[Test]
    public function trajectory_distance_with_complex_trajectories(): void
    {
        $dql = "SELECT TRAJECTORY_DISTANCE('LINESTRING M(0 0 0, 5 5 10)', 'LINESTRING M(1 1 0, 6 6 10)') as distance 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['distance']);
        $this->assertGreaterThanOrEqual(0, $result[0]['distance']);
    }
}
