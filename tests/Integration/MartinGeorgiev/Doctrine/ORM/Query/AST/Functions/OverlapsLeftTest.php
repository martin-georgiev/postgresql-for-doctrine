<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\OverlapsLeft;
use PHPUnit\Framework\Attributes\Test;

class OverlapsLeftTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVERLAPS_LEFT' => OverlapsLeft::class,
        ];
    }

    #[Test]
    public function returns_true_when_first_point_is_positioned_left_of_second(): void
    {
        $dql = 'SELECT OVERLAPS_LEFT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_geometry_positions_are_reversed(): void
    {
        $dql = 'SELECT OVERLAPS_LEFT(g.geometry2, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_first_polygon_overlaps_or_is_left_of_second(): void
    {
        $dql = 'SELECT OVERLAPS_LEFT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_comparing_identical_geometries(): void
    {
        $dql = 'SELECT OVERLAPS_LEFT(g.geometry1, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
