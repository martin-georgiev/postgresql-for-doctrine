<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BoundingBoxDistance;
use PHPUnit\Framework\Attributes\Test;

class BoundingBoxDistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'BOUNDING_BOX_DISTANCE' => BoundingBoxDistance::class,
        ];
    }

    #[Test]
    public function bounding_box_distance_returns_zero_for_identical_geometries(): void
    {
        // Identical geometries have zero bounding box distance
        $dql = "SELECT BOUNDING_BOX_DISTANCE('POINT(1 1)', 'POINT(1 1)') as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['distance'], 'Bounding box distance for identical geometries should be 0');
    }

    #[Test]
    public function bounding_box_distance_returns_zero_for_overlapping_bounding_boxes(): void
    {
        // Overlapping polygons have zero bounding box distance
        $dql = "SELECT BOUNDING_BOX_DISTANCE('POLYGON((0 0, 2 0, 2 2, 0 2, 0 0))', 'POLYGON((1 1, 3 1, 3 3, 1 3, 1 1))') as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['distance'], 'Overlapping bounding boxes should have distance 0');
    }

    #[Test]
    public function bounding_box_distance_returns_correct_distance_for_separated_geometries(): void
    {
        // Distance between separated points
        $dql = "SELECT BOUNDING_BOX_DISTANCE('POINT(0 0)', 'POINT(3 4)') as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(5.0, $result[0]['distance'], 'Bounding box distance between (0,0) and (3,4) should be 5');
    }

    #[Test]
    public function bounding_box_distance_with_separated_polygons(): void
    {
        // Distance between non-overlapping polygons
        $dql = "SELECT BOUNDING_BOX_DISTANCE('POLYGON((0 0, 1 0, 1 1, 0 1, 0 0))', 'POLYGON((3 3, 4 3, 4 4, 3 4, 3 3))') as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['distance']);
        $this->assertGreaterThan(0, $result[0]['distance']);
        // Distance should be approximately sqrt((3-1)² + (3-1)²) = sqrt(8) ≈ 2.83
        $this->assertEqualsWithDelta(2.83, $result[0]['distance'], 0.1, 'Distance between separated polygons');
    }
}
