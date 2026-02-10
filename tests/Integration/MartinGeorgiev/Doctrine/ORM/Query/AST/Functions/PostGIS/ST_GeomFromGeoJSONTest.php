<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsGeoJSON;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromGeoJSON;
use PHPUnit\Framework\Attributes\Test;

class ST_GeomFromGeoJSONTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMGEOJSON' => ST_GeomFromGeoJSON::class,
            'ST_ASGEOJSON' => ST_AsGeoJSON::class,
            'ST_EQUALS' => ST_Equals::class,
        ];
    }

    #[Test]
    public function round_trips_point_geometry(): void
    {
        $dql = "SELECT ST_EQUALS(g.geometry1, ST_GEOMFROMGEOJSON('{\"type\":\"Point\",\"coordinates\":[0,0]}')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function creates_geometry_from_geojson_point(): void
    {
        $dql = "SELECT ST_ASGEOJSON(ST_GEOMFROMGEOJSON('{\"type\":\"Point\",\"coordinates\":[-9.1393,38.7223]}')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $geojson = \json_decode($result[0]['result'], true);
        $this->assertIsArray($geojson);
        $this->assertSame('Point', $geojson['type']);
        $this->assertSame([-9.1393, 38.7223], $geojson['coordinates']);
    }

    #[Test]
    public function creates_geometry_from_geojson_with_parameter(): void
    {
        $dql = 'SELECT ST_ASGEOJSON(ST_GEOMFROMGEOJSON(:geojson)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql, [
            'geojson' => '{"type":"Point","coordinates":[-9.1393,38.7223]}',
        ]);
        $this->assertIsString($result[0]['result']);
        $geojson = \json_decode($result[0]['result'], true);
        $this->assertIsArray($geojson);
        $this->assertSame('Point', $geojson['type']);
        $this->assertSame([-9.1393, 38.7223], $geojson['coordinates']);
    }
}
