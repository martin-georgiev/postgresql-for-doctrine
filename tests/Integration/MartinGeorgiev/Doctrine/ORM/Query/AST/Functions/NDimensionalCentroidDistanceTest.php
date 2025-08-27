<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NDimensionalCentroidDistance;
use PHPUnit\Framework\Attributes\Test;

class NDimensionalCentroidDistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ND_CENTROID_DISTANCE' => NDimensionalCentroidDistance::class,
        ];
    }

    #[Test]
    public function can_calculate_distance_between_geometry_centroids(): void
    {
        $dql = 'SELECT ND_CENTROID_DISTANCE(g.geometry1, g.geometry2) as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['distance']);
        $this->assertGreaterThan(0, $result[0]['distance']);
    }

    #[Test]
    public function can_calculate_distance_between_geometry_and_literal_point(): void
    {
        $dql = "SELECT ND_CENTROID_DISTANCE(g.geometry1, 'POINT(3 3)') as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['distance']);
        $this->assertGreaterThan(0, $result[0]['distance']);
    }

    #[Test]
    public function returns_zero_when_comparing_identical_geometries(): void
    {
        $dql = 'SELECT ND_CENTROID_DISTANCE(g.geometry1, g.geometry1) as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['distance']);
    }
}
