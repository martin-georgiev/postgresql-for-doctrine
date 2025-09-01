<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_RelateMatch;

class ST_RelateMatchTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_RELATEMATCH' => ST_RelateMatch::class,
        ];
    }

    public function test_function_with_relate_match(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_RelateMatch(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_RelateMatch(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_'
        );
    }

    public function test_function_with_relate_match_in_where_clause(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_RelateMatch(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g WHERE ST_RelateMatch(g.geometry1, g.geometry2) = TRUE',
            'SELECT ST_RelateMatch(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_ WHERE ST_RelateMatch(c0_.geometry1, c0_.geometry2) = TRUE'
        );
    }

    public function test_function_with_no_relate_match(): void
    {
        $this->assertDoctrineQueryParsedToSql(
            'SELECT ST_RelateMatch(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g WHERE ST_RelateMatch(g.geometry1, g.geometry2) = FALSE',
            'SELECT ST_RelateMatch(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_ WHERE ST_RelateMatch(c0_.geometry1, c0_.geometry2) = FALSE'
        );
    }
}
