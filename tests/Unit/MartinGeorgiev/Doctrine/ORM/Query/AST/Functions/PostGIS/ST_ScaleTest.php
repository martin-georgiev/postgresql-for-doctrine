<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Scale;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class ST_ScaleTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_SCALE' => ST_Scale::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ST_Scale(c0_.geometry1, 2, 2) AS sclr_0 FROM ContainsGeometries c0_',
            'SELECT ST_Scale(c0_.geometry1, ?, ?) AS sclr_0 FROM ContainsGeometries c0_',
            'SELECT ST_Scale(c0_.geometry1, MIN(1), MIN(1)) AS sclr_0 FROM ContainsGeometries c0_',
            'SELECT ST_Scale(c0_.geometry1, 2, 2, 1) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'SELECT ST_SCALE(g.geometry1, 2, 2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_SCALE(g.geometry1, :dql_parameter1, :dql_parameter2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_SCALE(g.geometry1, MIN(1), MIN(1)) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_SCALE(g.geometry1, 2, 2, 1) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }
}
