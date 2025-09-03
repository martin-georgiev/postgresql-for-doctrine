<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\StrictlyBelow;
use PHPUnit\Framework\Attributes\Test;

class StrictlyBelowTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRICTLY_BELOW' => StrictlyBelow::class,
        ];
    }

    #[Test]
    public function strictly_below_returns_false_with_overlapping_polygons(): void
    {
        $dql = 'SELECT STRICTLY_BELOW(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result'], 'Overlapping polygons should not be strictly below each other');
    }

    #[Test]
    public function strictly_below_returns_true_when_geometry_is_lower(): void
    {
        $dql = 'SELECT STRICTLY_BELOW(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'POINT(0 0) should be strictly below POINT(1 1)');
    }

    #[Test]
    public function strictly_below_returns_true_with_linestrings(): void
    {
        $dql = 'SELECT STRICTLY_BELOW(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'Lower linestring should be strictly below higher linestring');
    }

    #[Test]
    public function strictly_below_returns_false_with_identical_geometries(): void
    {
        $dql = 'SELECT STRICTLY_BELOW(g.geometry1, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result'], 'Identical geometries should not be strictly below each other');
    }
}
