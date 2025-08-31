<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

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
    public function returns_zero_when_comparing_identical_point_geometries(): void
    {
        $dql = "SELECT BOUNDING_BOX_DISTANCE('POINT(1 1)', 'POINT(1 1)') as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['distance']);
    }

    #[Test]
    public function returns_zero_when_polygon_bounding_boxes_overlap(): void
    {
        $dql = "SELECT BOUNDING_BOX_DISTANCE('POLYGON((0 0, 2 0, 2 2, 0 2, 0 0))', 'POLYGON((1 1, 3 1, 3 3, 1 3, 1 1))') as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['distance']);
    }

    #[Test]
    public function can_calculate_euclidean_distance_between_separated_points(): void
    {
        $dql = "SELECT BOUNDING_BOX_DISTANCE('POINT(0 0)', 'POINT(3 4)') as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(5.0, $result[0]['distance']);
    }

    #[Test]
    public function can_calculate_distance_between_non_overlapping_polygons(): void
    {
        $dql = "SELECT BOUNDING_BOX_DISTANCE('POLYGON((0 0, 1 0, 1 1, 0 1, 0 0))', 'POLYGON((3 3, 4 3, 4 4, 3 4, 3 3))') as distance
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['distance']);
        $this->assertGreaterThan(0, $result[0]['distance']);
        $this->assertEqualsWithDelta(2.83, $result[0]['distance'], 0.1, 'Distance should be approximately sqrt((3-1)² + (3-1)²) = sqrt(8) ≈ 2.83 between separated polygon bounding boxes');
    }
}
