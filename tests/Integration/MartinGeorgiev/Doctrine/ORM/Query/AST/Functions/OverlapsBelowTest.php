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
    public function overlaps_below_with_geometries(): void
    {
        $dql = 'SELECT OVERLAPS_BELOW(g.geometry1, g.geometry2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'Overlapping or below geometries should return true');
    }

    #[Test]
    public function overlaps_below_with_literal_geometry(): void
    {
        $dql = "SELECT OVERLAPS_BELOW(g.geometry1, 'POINT(0 2)') as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'Overlapping or below geometries should return true');
    }

    #[Test]
    public function overlaps_below_with_polygons(): void
    {
        $dql = 'SELECT OVERLAPS_BELOW(g.geometry1, g.geometry2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'Overlapping or below geometries should return true');
    }
}
