<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_AsTextTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASTEXT' => ST_AsText::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts geometry' => 'SELECT ST_AsText(c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
            'converts geometry with max decimal digits' => 'SELECT ST_AsText(c0_.geometry1, 2) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts geometry' => \sprintf('SELECT ST_ASTEXT(g.geometry1) FROM %s g', ContainsGeometries::class),
            'converts geometry with max decimal digits' => \sprintf('SELECT ST_ASTEXT(g.geometry1, 2) FROM %s g', ContainsGeometries::class),
        ];
    }
}
