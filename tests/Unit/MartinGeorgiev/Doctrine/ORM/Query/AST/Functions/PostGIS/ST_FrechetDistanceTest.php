<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_FrechetDistance;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class ST_FrechetDistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_FRECHETDISTANCE' => ST_FrechetDistance::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ST_FrechetDistance(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
            'SELECT ST_FrechetDistance(c0_.geometry1, c0_.geometry2, 0.5) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'SELECT ST_FRECHETDISTANCE(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_FRECHETDISTANCE(g.geometry1, g.geometry2, 0.5) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }
}
