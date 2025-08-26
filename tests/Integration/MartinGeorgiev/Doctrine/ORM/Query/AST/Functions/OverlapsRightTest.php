<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\OverlapsRight;
use PHPUnit\Framework\Attributes\Test;

class OverlapsRightTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVERLAPS_RIGHT' => OverlapsRight::class,
        ];
    }

    #[Test]
    public function overlaps_right_returns_false_when_left_geometry_is_to_the_left(): void
    {
        $dql = 'SELECT OVERLAPS_RIGHT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result'], 'POINT(0 0) should NOT overlap or be to the right of POINT(1 1)');
    }

    #[Test]
    public function overlaps_right_returns_true_when_geometries_are_reversed(): void
    {
        $dql = 'SELECT OVERLAPS_RIGHT(g.geometry2, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'POINT(1 1) should overlap or be to the right of POINT(0 0)');
    }

    #[Test]
    public function overlaps_right_with_overlapping_polygons(): void
    {
        $dql = 'SELECT OVERLAPS_RIGHT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result'], 'Overlapping polygons may not be considered overlapping right depending on their positioning');
    }

    #[Test]
    public function overlaps_right_with_identical_geometries(): void
    {
        $dql = 'SELECT OVERLAPS_RIGHT(g.geometry1, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'Identical geometries should overlap');
    }
}
