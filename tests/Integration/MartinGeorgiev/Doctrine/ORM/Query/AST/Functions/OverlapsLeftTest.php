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
    public function overlaps_left_with_test_data(): void
    {
        $dql = 'SELECT OVERLAPS_LEFT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'POINT(0 0) should overlap or be to the left of POINT(1 1)');
    }

    #[Test]
    public function overlaps_left_with_reversed_geometries(): void
    {
        $dql = 'SELECT OVERLAPS_LEFT(g.geometry2, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result'], 'POINT(1 1) should NOT be to the left of POINT(0 0)');
    }

    #[Test]
    public function overlaps_left_with_overlapping_polygons(): void
    {
        $dql = 'SELECT OVERLAPS_LEFT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'First polygon should overlap or be to the left of second polygon');
    }

    #[Test]
    public function overlaps_left_with_identical_geometries(): void
    {
        $dql = 'SELECT OVERLAPS_LEFT(g.geometry1, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'Identical geometries should overlap');
    }
}
