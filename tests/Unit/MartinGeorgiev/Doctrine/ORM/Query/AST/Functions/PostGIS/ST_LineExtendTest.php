<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineExtend;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class ST_LineExtendTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LINEEXTEND' => ST_LineExtend::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ST_LineExtend(c0_.geometry1, 5) AS sclr_0 FROM ContainsGeometries c0_',
            'SELECT ST_LineExtend(c0_.geometry1, 5, 6) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'SELECT ST_LINEEXTEND(g.geometry1, 5) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_LINEEXTEND(g.geometry1, 5, 6) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }
}
