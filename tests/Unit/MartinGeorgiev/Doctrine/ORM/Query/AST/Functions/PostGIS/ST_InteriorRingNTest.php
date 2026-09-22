<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_InteriorRingN;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_InteriorRingNTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_INTERIORRINGN' => ST_InteriorRingN::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ST_InteriorRingN(c0_.geometry1, 1) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'SELECT ST_INTERIORRINGN(g.geometry1, 1) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }
}
