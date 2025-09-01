<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineCrossingDirection;

class ST_LineCrossingDirectionTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LINECROSSINGDIRECTION' => ST_LineCrossingDirection::class,
        ];
    }

    public function test_function_with_line_crossing_direction(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_LineCrossingDirection(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_LineCrossingDirection(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_'
        );
    }

    public function test_function_with_line_crossing_direction_in_where_clause(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_LineCrossingDirection(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g WHERE ST_LineCrossingDirection(g.geometry1, g.geometry2) = 1',
            'SELECT ST_LineCrossingDirection(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_ WHERE ST_LineCrossingDirection(c0_.geometry1, c0_.geometry2) = 1'
        );
    }

    public function test_function_with_no_line_crossing(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_LineCrossingDirection(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g WHERE ST_LineCrossingDirection(g.geometry1, g.geometry2) = 0',
            'SELECT ST_LineCrossingDirection(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_ WHERE ST_LineCrossingDirection(c0_.geometry1, c0_.geometry2) = 0'
        );
    }
}
