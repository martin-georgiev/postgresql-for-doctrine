<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\GeometryDistance;
use PHPUnit\Framework\Attributes\Test;

class GeometryDistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'GEOMETRY_DISTANCE' => GeometryDistance::class,
        ];
    }

    #[Test]
    public function can_calculate_euclidean_distance_between_geometric_points(): void
    {
        $dql = 'SELECT GEOMETRY_DISTANCE(g.geometry1, g.geometry2) as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['distance']);
        $this->assertGreaterThan(0, $result[0]['distance']);
        $this->assertEqualsWithDelta(1.414, $result[0]['distance'], 0.01, 'Euclidean distance between points (0,0) and (1,1) should be sqrt(2) ≈ 1.414');
    }

    #[Test]
    public function returns_zero_when_comparing_identical_geometries(): void
    {
        $dql = 'SELECT GEOMETRY_DISTANCE(g.geometry1, g.geometry1) as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['distance']);
    }

    #[Test]
    public function can_calculate_spherical_distance_between_geographical_coordinates(): void
    {
        $dql = 'SELECT GEOMETRY_DISTANCE(g.geography1, g.geography2) as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['distance']);
        $this->assertGreaterThan(1000000, $result[0]['distance'], 'Spherical distance between Lisbon and London geographical coordinates should be greater than 1 million meters');
    }
}
