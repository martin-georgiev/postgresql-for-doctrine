<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_StartPoint;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_StartPointTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_STARTPOINT' => ST_StartPoint::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ST_StartPoint(c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'SELECT ST_STARTPOINT(g.geometry1) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }
}
