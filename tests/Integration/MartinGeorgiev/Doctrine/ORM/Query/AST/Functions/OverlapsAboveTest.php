<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\OverlapsAbove;
use PHPUnit\Framework\Attributes\Test;

class OverlapsAboveTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVERLAPS_ABOVE' => OverlapsAbove::class,
        ];
    }

    #[Test]
    public function overlaps_above_with_geometries(): void
    {
        $dql = 'SELECT OVERLAPS_ABOVE(g.geometry1, g.geometry2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result'], 'Overlapping polygons may not be considered above each other');
    }

    #[Test]
    public function overlaps_above_with_literal_geometry(): void
    {
        $dql = "SELECT OVERLAPS_ABOVE(g.geometry1, 'POINT(0 -1)') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'Overlapping or above geometries should return true');
    }

    #[Test]
    public function overlaps_above_with_polygons(): void
    {
        $dql = 'SELECT OVERLAPS_ABOVE(g.geometry2, g.geometry1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'Overlapping or above geometries should return true');
    }
}
