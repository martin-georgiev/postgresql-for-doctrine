<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsEWKT;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_AsEWKTTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASEWKT' => ST_AsEWKT::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts geometry' => 'SELECT ST_AsEWKT(c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
            'converts geometry with max decimal digits' => 'SELECT ST_AsEWKT(c0_.geometry1, 2) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts geometry' => \sprintf('SELECT ST_ASEWKT(g.geometry1) FROM %s g', ContainsGeometries::class),
            'converts geometry with max decimal digits' => \sprintf('SELECT ST_ASEWKT(g.geometry1, 2) FROM %s g', ContainsGeometries::class),
        ];
    }
}
