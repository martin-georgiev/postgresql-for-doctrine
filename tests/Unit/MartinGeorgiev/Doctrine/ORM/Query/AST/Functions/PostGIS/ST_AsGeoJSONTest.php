<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsGeoJSON;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class ST_AsGeoJSONTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASGEOJSON' => ST_AsGeoJSON::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'with one argument' => 'SELECT ST_AsGeoJSON(c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
            'with two arguments' => 'SELECT ST_AsGeoJSON(c0_.geometry1, 6) AS sclr_0 FROM ContainsGeometries c0_',
            'with three arguments' => 'SELECT ST_AsGeoJSON(c0_.geometry1, 6, 2) AS sclr_0 FROM ContainsGeometries c0_',
            'with named parameter' => 'SELECT ST_AsGeoJSON(c0_.geometry1, ?) AS sclr_0 FROM ContainsGeometries c0_',
            'with function expression' => 'SELECT ST_AsGeoJSON(c0_.geometry1, MIN(1)) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'with one argument' => \sprintf('SELECT ST_ASGEOJSON(g.geometry1) FROM %s g', ContainsGeometries::class),
            'with two arguments' => \sprintf('SELECT ST_ASGEOJSON(g.geometry1, 6) FROM %s g', ContainsGeometries::class),
            'with three arguments' => \sprintf('SELECT ST_ASGEOJSON(g.geometry1, 6, 2) FROM %s g', ContainsGeometries::class),
            'with named parameter' => \sprintf('SELECT ST_ASGEOJSON(g.geometry1, :dql_parameter) FROM %s g', ContainsGeometries::class),
            'with function expression' => \sprintf('SELECT ST_ASGEOJSON(g.geometry1, MIN(1)) FROM %s g', ContainsGeometries::class),
        ];
    }
}
