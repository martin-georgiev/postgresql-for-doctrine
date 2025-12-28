<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CoverageUnion;
use PHPUnit\Framework\Attributes\Test;

class ST_CoverageUnionTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_COVERAGEUNION' => ST_CoverageUnion::class,
        ];
    }

    #[Test]
    public function computes_union_preserves_total_area(): void
    {
        // ST_CoverageUnion is an aggregate function - when applied to a single polygon,
        // it should return the same polygon with the same area (16 for 4x4 polygon)
        $dql = 'SELECT ST_AREA(ST_COVERAGEUNION(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2
                GROUP BY g.id';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result'], 'coverage union should preserve area of 4x4 polygon');
    }

    #[Test]
    public function computes_union_of_adjacent_polygons(): void
    {
        // id=4: POLYGON((0 0, 0 2, 2 2, 2 0, 0 0)) - 2x2 polygon (area=4)
        // id=13: POLYGON((2 0, 2 2, 4 2, 4 0, 2 0)) - adjacent 2x2 polygon (area=4)
        // Together they form a valid coverage with combined area=8
        $dql = 'SELECT ST_AREA(ST_COVERAGEUNION(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id IN (4, 13)';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(8, $result[0]['result']);
    }
}
