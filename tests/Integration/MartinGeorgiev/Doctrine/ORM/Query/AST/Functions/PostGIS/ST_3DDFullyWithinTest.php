<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DDFullyWithin;

class ST_3DDFullyWithinTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_3DDFULLYWITHIN' => ST_3DDFullyWithin::class,
        ];
    }

    public function test_function_with_3d_fully_within_distance(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_3DDFullyWithin(g.geometry1, g.geometry2, 1000) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_3DDFullyWithin(c0_.geometry1, c0_.geometry2, 1000) AS sclr_0 FROM ContainsGeometries c0_'
        );
    }

    public function test_function_with_3d_fully_within_distance_in_where_clause(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_3DDFullyWithin(g.geometry1, g.geometry2, 1000) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g WHERE ST_3DDFullyWithin(g.geometry1, g.geometry2, 1000) = TRUE',
            'SELECT ST_3DDFullyWithin(c0_.geometry1, c0_.geometry2, 1000) AS sclr_0 FROM ContainsGeometries c0_ WHERE ST_3DDFullyWithin(c0_.geometry1, c0_.geometry2, 1000) = TRUE'
        );
    }

    public function test_function_with_not_3d_fully_within_distance(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_3DDFullyWithin(g.geometry1, g.geometry2, 1000) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g WHERE ST_3DDFullyWithin(g.geometry1, g.geometry2, 1000) = FALSE',
            'SELECT ST_3DDFullyWithin(c0_.geometry1, c0_.geometry2, 1000) AS sclr_0 FROM ContainsGeometries c0_ WHERE ST_3DDFullyWithin(c0_.geometry1, c0_.geometry2, 1000) = FALSE'
        );
    }
}
