<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsGeoJSON;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Transform;
use PHPUnit\Framework\Attributes\Test;

class ST_TransformTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASGEOJSON' => ST_AsGeoJSON::class,
            'ST_EQUALS' => ST_Equals::class,
            'ST_TRANSFORM' => ST_Transform::class,
        ];
    }

    #[Test]
    public function transforms_polygon_from_wgs84_to_web_mercator(): void
    {
        $dql = "SELECT ST_ASGEOJSON(ST_TRANSFORM(g.geometry1, ABS(3857))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $geojson = \json_decode((string) $result[0]['result'], true);
        $this->assertSame('Polygon', $geojson['type']);
        $vertex = $geojson['coordinates'][0][2];
        $this->assertEqualsWithDelta(445277.96, $vertex[0], 1.0);
        $this->assertEqualsWithDelta(445640.11, $vertex[1], 1.0);
    }

    #[Test]
    public function transforms_linestring_from_web_mercator_to_wgs84(): void
    {
        $dql = "SELECT ST_ASGEOJSON(ST_TRANSFORM(g.geometry1, ABS(4326))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 10";

        $result = $this->executeDqlQuery($dql);
        $geojson = \json_decode((string) $result[0]['result'], true);
        $this->assertSame('LineString', $geojson['type']);
        $this->assertEqualsWithDelta(0.0, $geojson['coordinates'][0][0], 0.01);
        $this->assertEqualsWithDelta(0.0, $geojson['coordinates'][0][1], 0.01);
        $this->assertEqualsWithDelta(0.00898, $geojson['coordinates'][1][0], 0.001);
        $this->assertEqualsWithDelta(0.0, $geojson['coordinates'][1][1], 0.01);
    }

    #[Test]
    public function round_trip_transform_returns_equivalent_geometry(): void
    {
        $dql = "SELECT ST_ASGEOJSON(g.geometry1) as original, 
                       ST_ASGEOJSON(ST_TRANSFORM(ST_TRANSFORM(g.geometry1, ABS(4326)), ABS(3857))) as transformed
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 10";

        $result = $this->executeDqlQuery($dql);
        $original = \json_decode((string) $result[0]['original'], true);
        $transformed = \json_decode((string) $result[0]['transformed'], true);

        $this->assertSame($original['type'], $transformed['type']);
        $this->assertEqualsWithDelta($original['coordinates'][0][0], $transformed['coordinates'][0][0], 0.01);
        $this->assertEqualsWithDelta($original['coordinates'][0][1], $transformed['coordinates'][0][1], 0.01);
    }

    #[Test]
    public function cross_srid_transform_changes_coordinates(): void
    {
        $dql = "SELECT ST_ASGEOJSON(g.geometry1) as original,
                       ST_ASGEOJSON(ST_TRANSFORM(g.geometry1, ABS(3857))) as transformed
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $original = \json_decode((string) $result[0]['original'], true);
        $transformed = \json_decode((string) $result[0]['transformed'], true);

        $this->assertSame('Polygon', $original['type']);
        $this->assertSame('Polygon', $transformed['type']);
        $originalVertex = $original['coordinates'][0][2];
        $transformedVertex = $transformed['coordinates'][0][2];
        $this->assertNotEquals($originalVertex[0], $transformedVertex[0]);
        $this->assertNotEquals($originalVertex[1], $transformedVertex[1]);
    }

    #[Test]
    public function transforms_with_function_expression(): void
    {
        $dql = "SELECT ST_ASGEOJSON(ST_TRANSFORM(g.geometry1, ABS(4326))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 10";

        $result = $this->executeDqlQuery($dql);
        $geojson = \json_decode((string) $result[0]['result'], true);
        $this->assertSame('LineString', $geojson['type']);
        $this->assertEqualsWithDelta(0.0, $geojson['coordinates'][0][0], 0.01);
    }

    #[Test]
    public function transforms_with_proj_string(): void
    {
        $dql = "SELECT ST_ASGEOJSON(ST_TRANSFORM(g.geometry1, '+proj=longlat +datum=WGS84')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 10";

        $result = $this->executeDqlQuery($dql);
        $geojson = \json_decode((string) $result[0]['result'], true);
        $this->assertSame('LineString', $geojson['type']);
        $this->assertEqualsWithDelta(0.0, $geojson['coordinates'][0][0], 0.01);
        $this->assertEqualsWithDelta(0.0, $geojson['coordinates'][0][1], 0.01);
    }
}
