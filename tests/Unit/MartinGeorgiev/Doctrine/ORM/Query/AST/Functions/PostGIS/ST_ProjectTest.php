<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Project;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class ST_ProjectTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_PROJECT' => ST_Project::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'projects with literal values' => 'SELECT ST_Project(c0_.geometry1, 1000, 0.785398) AS sclr_0 FROM ContainsGeometries c0_',
            'projects with arithmetic expressions' => 'SELECT ST_Project(c0_.geometry1, 1000 * 2, 0.785398 + 0.1) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'projects with literal values' => 'SELECT ST_PROJECT(g.geometry1, 1000, 0.785398) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'projects with arithmetic expressions' => 'SELECT ST_PROJECT(g.geometry1, 1000 * 2, 0.785398 + 0.1) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }
}
