<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_EndPoint;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_EndPointTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ENDPOINT' => ST_EndPoint::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ST_EndPoint(c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'SELECT ST_ENDPOINT(g.geometry1) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }
}
