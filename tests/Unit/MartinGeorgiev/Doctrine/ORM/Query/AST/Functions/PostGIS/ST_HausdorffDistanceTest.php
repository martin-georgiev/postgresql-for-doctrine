<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_HausdorffDistance;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class ST_HausdorffDistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_HAUSDORFFDISTANCE' => ST_HausdorffDistance::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ST_HausdorffDistance(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'SELECT ST_HAUSDORFFDISTANCE(g.geometry1, g.geometry2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }
}
