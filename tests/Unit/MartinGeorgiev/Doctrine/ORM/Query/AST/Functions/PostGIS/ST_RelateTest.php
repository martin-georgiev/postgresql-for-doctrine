<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Relate;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunctionTestCase;

class ST_RelateTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new ST_Relate('ST_Relate');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ST_RELATE' => ST_Relate::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'returns DE-9IM intersection matrix between geometries' => 'SELECT ST_Relate(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
            'tests geometries against custom DE-9IM pattern matrix' => "SELECT ST_Relate(c0_.geometry1, c0_.geometry2, 'T*T***T**') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'returns DE-9IM intersection matrix between geometries' => \sprintf('SELECT ST_RELATE(g.geometry1, g.geometry2) FROM %s g', ContainsGeometries::class),
            'tests geometries against custom DE-9IM pattern matrix' => \sprintf("SELECT ST_RELATE(g.geometry1, g.geometry2, 'T*T***T**') FROM %s g", ContainsGeometries::class),
        ];
    }
}
