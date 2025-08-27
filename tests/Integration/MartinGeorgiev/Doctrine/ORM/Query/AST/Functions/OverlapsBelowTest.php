<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\OverlapsBelow;
use PHPUnit\Framework\Attributes\Test;

class OverlapsBelowTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVERLAPS_BELOW' => OverlapsBelow::class,
        ];
    }

    #[Test]
    public function returns_true_when_first_polygon_overlaps_or_is_below_second(): void
    {
        $dql = 'SELECT OVERLAPS_BELOW(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_geometry_is_positioned_below_literal_point(): void
    {
        $dql = "SELECT OVERLAPS_BELOW(g.geometry1, 'POINT(0 2)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_comparing_overlapping_polygons(): void
    {
        $dql = 'SELECT OVERLAPS_BELOW(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
