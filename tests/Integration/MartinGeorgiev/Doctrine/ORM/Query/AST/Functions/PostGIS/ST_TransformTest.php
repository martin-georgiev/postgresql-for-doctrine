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
        $dql = "SELECT ST_ASGEOJSON(ST_TRANSFORM(g.geometry1, 3857)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $geojson = \json_decode($result[0]['result'], true);
        $this->assertIsArray($geojson);
        $this->assertSame('Polygon', $geojson['type']);
        $this->assertIsArray($geojson['coordinates']);
        $outerRing = $geojson['coordinates'][0];
        $this->assertIsArray($outerRing);
        $vertex = $outerRing[2];
        $this->assertIsArray($vertex);
        $this->assertEqualsWithDelta(445277.96, $vertex[0], 1.0);
        $this->assertEqualsWithDelta(445640.11, $vertex[1], 1.0);
    }

    #[Test]
    public function transforms_linestring_from_web_mercator_to_wgs84(): void
    {
        $dql = "SELECT ST_ASGEOJSON(ST_TRANSFORM(g.geometry1, 4326)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 10";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $geojson = \json_decode($result[0]['result'], true);
        $this->assertIsArray($geojson);
        $this->assertSame('LineString', $geojson['type']);
        $this->assertIsArray($geojson['coordinates']);
        $coords = $geojson['coordinates'];
        $firstPoint = $coords[0];
        $secondPoint = $coords[1];
        $this->assertIsArray($firstPoint);
        $this->assertIsArray($secondPoint);
        $this->assertEqualsWithDelta(0.0, $firstPoint[0], 0.01);
        $this->assertEqualsWithDelta(0.0, $firstPoint[1], 0.01);
        $this->assertEqualsWithDelta(0.00898, $secondPoint[0], 0.001);
        $this->assertEqualsWithDelta(0.0, $secondPoint[1], 0.01);
    }

    #[Test]
    public function round_trip_transform_returns_equivalent_geometry(): void
    {
        $dql = "SELECT ST_ASGEOJSON(g.geometry1) as original,
                       ST_ASGEOJSON(ST_TRANSFORM(ST_TRANSFORM(g.geometry1, 4326), 3857)) as transformed
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 10";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['original']);
        $this->assertIsString($result[0]['transformed']);
        $original = \json_decode($result[0]['original'], true);
        $transformed = \json_decode($result[0]['transformed'], true);
        $this->assertIsArray($original);
        $this->assertIsArray($transformed);
        $this->assertIsArray($original['coordinates']);
        $this->assertIsArray($transformed['coordinates']);
        $originalCoordinates = $original['coordinates'];
        $transformedCoordinates = $transformed['coordinates'];
        $originalFirstPoint = $originalCoordinates[0];
        $transformedFirstPoint = $transformedCoordinates[0];
        $this->assertIsArray($originalFirstPoint);
        $this->assertIsArray($transformedFirstPoint);

        $this->assertSame($original['type'], $transformed['type']);
        $this->assertEqualsWithDelta($originalFirstPoint[0], $transformedFirstPoint[0], 0.01);
        $this->assertEqualsWithDelta($originalFirstPoint[1], $transformedFirstPoint[1], 0.01);
    }

    #[Test]
    public function cross_srid_transform_changes_coordinates(): void
    {
        $dql = "SELECT ST_ASGEOJSON(g.geometry1) as original,
                       ST_ASGEOJSON(ST_TRANSFORM(g.geometry1, 3857)) as transformed
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['original']);
        $this->assertIsString($result[0]['transformed']);
        $original = \json_decode($result[0]['original'], true);
        $transformed = \json_decode($result[0]['transformed'], true);
        $this->assertIsArray($original);
        $this->assertIsArray($transformed);
        $this->assertIsArray($original['coordinates']);
        $this->assertIsArray($transformed['coordinates']);
        $origRing = $original['coordinates'][0];
        $transRing = $transformed['coordinates'][0];
        $this->assertIsArray($origRing);
        $this->assertIsArray($transRing);
        $originalVertex = $origRing[2];
        $transformedVertex = $transRing[2];
        $this->assertIsArray($originalVertex);
        $this->assertIsArray($transformedVertex);

        $origX = $originalVertex[0];
        $origY = $originalVertex[1];
        $transX = $transformedVertex[0];
        $transY = $transformedVertex[1];

        $this->assertSame('Polygon', $original['type']);
        $this->assertSame('Polygon', $transformed['type']);
        $this->assertNotEquals($origX, $transX);
        $this->assertNotEquals($origY, $transY);
    }

    #[Test]
    public function transforms_with_function_expression(): void
    {
        $dql = "SELECT ST_ASGEOJSON(ST_TRANSFORM(g.geometry1, 4326)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 10";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $geojson = \json_decode($result[0]['result'], true);
        $this->assertIsArray($geojson);
        $this->assertSame('LineString', $geojson['type']);
        $this->assertIsArray($geojson['coordinates']);
        $coords = $geojson['coordinates'];
        $firstPoint = $coords[0];
        $this->assertIsArray($firstPoint);
        $this->assertEqualsWithDelta(0.0, $firstPoint[0], 0.01);
    }

    #[Test]
    public function transforms_with_proj_string(): void
    {
        $dql = "SELECT ST_ASGEOJSON(ST_TRANSFORM(g.geometry1, '+proj=longlat +datum=WGS84')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 10";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $geojson = \json_decode($result[0]['result'], true);
        $this->assertIsArray($geojson);
        $this->assertSame('LineString', $geojson['type']);
        $this->assertIsArray($geojson['coordinates']);
        $coords = $geojson['coordinates'];
        $firstPoint = $coords[0];
        $this->assertIsArray($firstPoint);
        $this->assertEqualsWithDelta(0.0, $firstPoint[0], 0.01);
        $this->assertEqualsWithDelta(0.0, $firstPoint[1], 0.01);
    }
}
