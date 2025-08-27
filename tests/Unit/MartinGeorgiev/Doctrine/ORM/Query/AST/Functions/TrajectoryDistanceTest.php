<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TrajectoryDistance;

class TrajectoryDistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TRAJECTORY_DISTANCE' => TrajectoryDistance::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'calculates trajectory distance between geometries' => 'SELECT (c0_.geometry1 |=| c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'calculates trajectory distance between geometries' => \sprintf('SELECT TRAJECTORY_DISTANCE(e.geometry1, e.geometry2) FROM %s e', ContainsGeometries::class),
        ];
    }
}
