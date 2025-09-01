<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Overlaps;

class ST_OverlapsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_OVERLAPS' => ST_Overlaps::class,
        ];
    }

    public function test_function_with_overlapping_geometries(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_Overlaps(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_Overlaps(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_'
        );
    }

    public function test_function_with_overlapping_geometries_in_where_clause(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_Overlaps(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g WHERE ST_Overlaps(g.geometry1, g.geometry2) = TRUE',
            'SELECT ST_Overlaps(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_ WHERE ST_Overlaps(c0_.geometry1, c0_.geometry2) = TRUE'
        );
    }

    public function test_function_with_non_overlapping_geometries(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_Overlaps(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g WHERE ST_Overlaps(g.geometry1, g.geometry2) = FALSE',
            'SELECT ST_Overlaps(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_ WHERE ST_Overlaps(c0_.geometry1, c0_.geometry2) = FALSE'
        );
    }
}
