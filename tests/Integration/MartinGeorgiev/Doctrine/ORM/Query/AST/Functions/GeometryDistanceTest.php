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
    public function geometry_distance_returns_correct_distance_between_test_points(): void
    {
        // Distance between POINT(0 0) and POINT(1 1) from test data
        $dql = 'SELECT GEOMETRY_DISTANCE(g.geometry1, g.geometry2) as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['distance']);
        $this->assertGreaterThan(0, $result[0]['distance']);
        // Distance between (0,0) and (1,1) should be sqrt(2) ≈ 1.414
        $this->assertEqualsWithDelta(1.414, $result[0]['distance'], 0.01, 'Distance between (0,0) and (1,1)');
    }

    #[Test]
    public function geometry_distance_returns_zero_for_identical_geometries(): void
    {
        // Identical geometries should have zero distance
        $dql = 'SELECT GEOMETRY_DISTANCE(g.geometry1, g.geometry1) as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['distance'], 'Distance between identical geometries should be 0');
    }

    #[Test]
    public function geometry_distance_with_horizontal_points(): void
    {
        // Distance between POINT(0 0) and POINT(5 0) should be 5
        $dql = "SELECT GEOMETRY_DISTANCE('POINT(0 0)', 'POINT(5 0)') as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(5.0, $result[0]['distance'], 'Horizontal distance should be 5');
    }

    #[Test]
    public function geometry_distance_with_vertical_points(): void
    {
        // Distance between POINT(0 0) and POINT(0 3) should be 3
        $dql = "SELECT GEOMETRY_DISTANCE('POINT(0 0)', 'POINT(0 3)') as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3.0, $result[0]['distance'], 'Vertical distance should be 3');
    }

    #[Test]
    public function geometry_distance_with_geography_types(): void
    {
        // Geography types should return distance in meters
        $dql = 'SELECT GEOMETRY_DISTANCE(g.geography1, g.geography2) as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['distance']);
        $this->assertGreaterThan(1000000, $result[0]['distance']); // Lisbon to London is > 1M meters
    }
}
