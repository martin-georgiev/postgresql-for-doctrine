<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CoverageUnion;
use PHPUnit\Framework\Attributes\Test;

final class ST_CoverageUnionTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_COVERAGEUNION' => ST_CoverageUnion::class,
        ];
    }

    #[Test]
    public function preserves_total_area_in_coverage_union(): void
    {
        // ST_CoverageUnion is an aggregate function - when applied to a single polygon,
        // it should return the same polygon with the same area (16 for 4x4 polygon)
        $dql = 'SELECT ST_AREA(ST_COVERAGEUNION(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2
                GROUP BY g.id';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }
}
