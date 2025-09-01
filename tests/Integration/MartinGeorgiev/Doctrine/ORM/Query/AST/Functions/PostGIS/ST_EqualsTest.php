<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;

class ST_EqualsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_EQUALS' => ST_Equals::class,
        ];
    }

    public function test_function_with_equal_geometries(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_Equals(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_Equals(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_'
        );
    }

    public function test_function_with_equal_geometries_in_where_clause(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_Equals(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g WHERE ST_Equals(g.geometry1, g.geometry2) = TRUE',
            'SELECT ST_Equals(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_ WHERE ST_Equals(c0_.geometry1, c0_.geometry2) = TRUE'
        );
    }

    public function test_function_with_non_equal_geometries(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_Equals(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g WHERE ST_Equals(g.geometry1, g.geometry2) = FALSE',
            'SELECT ST_Equals(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_ WHERE ST_Equals(c0_.geometry1, c0_.geometry2) = FALSE'
        );
    }
}
