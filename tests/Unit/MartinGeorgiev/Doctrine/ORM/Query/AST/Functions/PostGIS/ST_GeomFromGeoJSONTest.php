<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromGeoJSON;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class ST_GeomFromGeoJSONTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMGEOJSON' => ST_GeomFromGeoJSON::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'with GeoJSON string literal' => "SELECT ST_GeomFromGeoJSON('{\"type\":\"Point\",\"coordinates\":[0,0]}') AS sclr_0 FROM ContainsGeometries c0_",
            'with named parameter' => 'SELECT ST_GeomFromGeoJSON(?) AS sclr_0 FROM ContainsGeometries c0_',
            'with field reference' => 'SELECT ST_GeomFromGeoJSON(c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'with GeoJSON string literal' => \sprintf("SELECT ST_GEOMFROMGEOJSON('{\"type\":\"Point\",\"coordinates\":[0,0]}') FROM %s g", ContainsGeometries::class),
            'with named parameter' => \sprintf('SELECT ST_GEOMFROMGEOJSON(:dql_parameter) FROM %s g', ContainsGeometries::class),
            'with field reference' => \sprintf('SELECT ST_GEOMFROMGEOJSON(g.geometry1) FROM %s g', ContainsGeometries::class),
        ];
    }
}
