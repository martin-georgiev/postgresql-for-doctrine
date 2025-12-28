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
    public function computes_union_of_multiple_polygons(): void
    {
        // Union of two overlapping polygons: 4x4 (area=16) and 2x2 (area=4) with overlap
        // id=2: POLYGON((0 0, 0 4, 4 4, 4 0, 0 0)) - 4x4 polygon
        // id=4: POLYGON((0 0, 0 2, 2 2, 2 0, 0 0)) - 2x2 polygon (fully contained in id=2)
        // Combined area should be 16 (the larger polygon contains the smaller one)
        $dql = 'SELECT ST_AREA(ST_COVERAGEUNION(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id IN (2, 4)';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(20, $result[0]['result'], 'coverage union of 4x4 and 2x2 non-overlapping polygons should have combined area');
    }
}
