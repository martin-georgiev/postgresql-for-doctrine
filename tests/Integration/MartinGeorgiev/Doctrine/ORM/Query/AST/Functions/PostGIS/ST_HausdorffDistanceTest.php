<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_HausdorffDistance;
use PHPUnit\Framework\Attributes\Test;

class ST_HausdorffDistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_HAUSDORFFDISTANCE' => ST_HausdorffDistance::class,
        ];
    }

    #[Test]
    public function returns_hausdorff_distance_between_points(): void
    {
        $dql = 'SELECT ST_HAUSDORFFDISTANCE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.4142135623730951, $result[0]['result'], 0.0000000000000001);
    }

    #[Test]
    public function returns_zero_for_identical_geometries(): void
    {
        $dql = 'SELECT ST_HAUSDORFFDISTANCE(g.geometry1, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function returns_hausdorff_distance_between_polygons(): void
    {
        $dql = 'SELECT ST_HAUSDORFFDISTANCE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.4142135623730951, $result[0]['result'], 0.0000000000000001);
    }

    #[Test]
    public function returns_hausdorff_distance_between_polygons_with_densify_frac_parameter(): void
    {
        $dql = 'SELECT ST_HAUSDORFFDISTANCE(g.geometry1, g.geometry2, 0.8) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.4142135623730951, $result[0]['result'], 0.0000000000000001);
    }
}
