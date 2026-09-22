<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromEWKT;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_GeomFromEWKTTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMEWKT' => ST_GeomFromEWKT::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'with EWKT string literal' => "SELECT ST_GeomFromEWKT('SRID=4326;POINT(1 2)') AS sclr_0 FROM ContainsGeometries c0_",
            'with named parameter' => 'SELECT ST_GeomFromEWKT(?) AS sclr_0 FROM ContainsGeometries c0_',
            'with field reference' => 'SELECT ST_GeomFromEWKT(c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'with EWKT string literal' => \sprintf("SELECT ST_GEOMFROMEWKT('SRID=4326;POINT(1 2)') FROM %s g", ContainsGeometries::class),
            'with named parameter' => \sprintf('SELECT ST_GEOMFROMEWKT(:dql_parameter) FROM %s g', ContainsGeometries::class),
            'with field reference' => \sprintf('SELECT ST_GEOMFROMEWKT(g.geometry1) FROM %s g', ContainsGeometries::class),
        ];
    }
}
