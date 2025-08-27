<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\GeometryDistance;

class GeometryDistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'GEOMETRY_DISTANCE' => GeometryDistance::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'calculates distance between geometries' => 'SELECT (c0_.geometry1 <-> c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
            'calculates distance between geometry and literal' => "SELECT (c0_.geometry1 <-> 'POINT(1 2)') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'calculates distance between geometries' => \sprintf('SELECT GEOMETRY_DISTANCE(e.geometry1, e.geometry2) FROM %s e', ContainsGeometries::class),
            'calculates distance between geometry and literal' => \sprintf("SELECT GEOMETRY_DISTANCE(e.geometry1, 'POINT(1 2)') FROM %s e", ContainsGeometries::class),
        ];
    }
}
