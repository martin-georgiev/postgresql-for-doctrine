<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\StrictlyAbove;
use PHPUnit\Framework\Attributes\Test;

class StrictlyAboveTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRICTLY_ABOVE' => StrictlyAbove::class,
        ];
    }

    #[Test]
    public function strictly_above_returns_false_with_overlapping_polygons(): void
    {
        // Overlapping polygons are not strictly above each other
        $dql = 'SELECT STRICTLY_ABOVE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result'], 'Overlapping polygons should not be strictly above each other');
    }

    #[Test]
    public function strictly_above_returns_false_with_points_at_same_level(): void
    {
        // POINT(0 0) is not strictly above POINT(1 1) - they are at similar Y coordinates
        $dql = 'SELECT STRICTLY_ABOVE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result'], 'POINT(0 0) should not be strictly above POINT(1 1)');
    }

    #[Test]
    public function strictly_above_returns_true_when_geometry_is_higher(): void
    {
        // Test with linestrings where second has higher Y coordinates
        $dql = 'SELECT STRICTLY_ABOVE(g.geometry2, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'Higher linestring should be strictly above lower linestring');
    }

    #[Test]
    public function strictly_above_returns_false_with_identical_geometries(): void
    {
        // Identical geometries are not strictly above each other
        $dql = 'SELECT STRICTLY_ABOVE(g.geometry1, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result'], 'Identical geometries should not be strictly above each other');
    }
}
