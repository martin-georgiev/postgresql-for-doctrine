<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsGeoJSON;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_AsGeoJSONTest extends TestCase
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
            'converts geometry' => 'SELECT ST_AsGeoJSON(c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
            'converts geometry with max decimal digits' => 'SELECT ST_AsGeoJSON(c0_.geometry1, 6) AS sclr_0 FROM ContainsGeometries c0_',
            'converts geometry with options' => 'SELECT ST_AsGeoJSON(c0_.geometry1, 6, 2) AS sclr_0 FROM ContainsGeometries c0_',
            'converts geometry with named parameter' => 'SELECT ST_AsGeoJSON(c0_.geometry1, ?) AS sclr_0 FROM ContainsGeometries c0_',
            'converts geometry with function expression' => 'SELECT ST_AsGeoJSON(c0_.geometry1, MIN(1)) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts geometry' => \sprintf('SELECT ST_ASGEOJSON(g.geometry1) FROM %s g', ContainsGeometries::class),
            'converts geometry with max decimal digits' => \sprintf('SELECT ST_ASGEOJSON(g.geometry1, 6) FROM %s g', ContainsGeometries::class),
            'converts geometry with options' => \sprintf('SELECT ST_ASGEOJSON(g.geometry1, 6, 2) FROM %s g', ContainsGeometries::class),
            'converts geometry with named parameter' => \sprintf('SELECT ST_ASGEOJSON(g.geometry1, :dql_parameter) FROM %s g', ContainsGeometries::class),
            'converts geometry with function expression' => \sprintf('SELECT ST_ASGEOJSON(g.geometry1, MIN(1)) FROM %s g', ContainsGeometries::class),
        ];
    }
}
