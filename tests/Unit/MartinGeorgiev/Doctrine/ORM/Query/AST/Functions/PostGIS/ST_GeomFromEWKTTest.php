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
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'with EWKT string literal' => \sprintf("SELECT ST_GEOMFROMEWKT('SRID=4326;POINT(1 2)') FROM %s g", ContainsGeometries::class),
        ];
    }
}
