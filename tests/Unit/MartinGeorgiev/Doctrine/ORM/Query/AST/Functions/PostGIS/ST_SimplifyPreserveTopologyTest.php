<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SimplifyPreserveTopology;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class ST_SimplifyPreserveTopologyTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_SIMPLIFYPRESERVETOPOLOGY' => ST_SimplifyPreserveTopology::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ST_SimplifyPreserveTopology(c0_.geometry1, 0.5) AS sclr_0 FROM ContainsGeometries c0_',
            'SELECT ST_SimplifyPreserveTopology(c0_.geometry1, ?) AS sclr_0 FROM ContainsGeometries c0_',
            'SELECT ST_SimplifyPreserveTopology(c0_.geometry1, MIN(1)) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'SELECT ST_SIMPLIFYPRESERVETOPOLOGY(g.geometry1, 0.5) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_SIMPLIFYPRESERVETOPOLOGY(g.geometry1, :dql_parameter) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_SIMPLIFYPRESERVETOPOLOGY(g.geometry1, MIN(1)) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }
}
