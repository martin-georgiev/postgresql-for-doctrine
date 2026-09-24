<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Intersection;
use PHPUnit\Framework\Attributes\Test;

final class ST_IntersectionTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_INTERSECTION' => ST_Intersection::class,
        ];
    }

    #[Test]
    public function returns_intersection_of_overlapping_polygons(): void
    {
        $dql = 'SELECT ST_AREA(ST_INTERSECTION(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 4';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result']);
    }
}
