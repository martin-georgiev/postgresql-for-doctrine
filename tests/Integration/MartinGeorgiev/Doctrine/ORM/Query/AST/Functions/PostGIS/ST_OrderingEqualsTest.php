<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_OrderingEquals;

class ST_OrderingEqualsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ORDERINGEQUALS' => ST_OrderingEquals::class,
        ];
    }

    public function test_function_with_ordering_equal_geometries(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_OrderingEquals(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_OrderingEquals(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_'
        );
    }

    public function test_function_with_ordering_equal_geometries_in_where_clause(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_OrderingEquals(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g WHERE ST_OrderingEquals(g.geometry1, g.geometry2) = TRUE',
            'SELECT ST_OrderingEquals(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_ WHERE ST_OrderingEquals(c0_.geometry1, c0_.geometry2) = TRUE'
        );
    }

    public function test_function_with_non_ordering_equal_geometries(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_OrderingEquals(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g WHERE ST_OrderingEquals(g.geometry1, g.geometry2) = FALSE',
            'SELECT ST_OrderingEquals(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_ WHERE ST_OrderingEquals(c0_.geometry1, c0_.geometry2) = FALSE'
        );
    }
}
