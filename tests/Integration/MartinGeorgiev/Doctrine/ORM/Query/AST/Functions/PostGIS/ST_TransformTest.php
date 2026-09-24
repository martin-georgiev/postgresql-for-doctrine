<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsGeoJSON;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Transform;
use PHPUnit\Framework\Attributes\Test;

final class ST_TransformTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASGEOJSON' => ST_AsGeoJSON::class,
            'ST_TRANSFORM' => ST_Transform::class,
        ];
    }

    #[Test]
    public function returns_the_transformed_geometry_from_an_entity_field(): void
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
        $this->assertEqualsWithDelta(445277.96, $vertex[0], 0.01);
        $this->assertEqualsWithDelta(445640.11, $vertex[1], 0.01);
    }

    #[Test]
    public function converts_geometry_with_proj_string(): void
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
        $coordinates = $geojson['coordinates'];
        $firstPoint = $coordinates[0];
        $this->assertIsArray($firstPoint);
        $this->assertEqualsWithDelta(0.0, $firstPoint[0], 0.0000000001);
        $this->assertEqualsWithDelta(0.0, $firstPoint[1], 0.0000000001);
    }

    #[Test]
    public function returns_the_transformed_geometry_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_ASGEOJSON(ST_TRANSFORM('SRID=4326;POLYGON((0 0, 0 4, 4 4, 4 0, 0 0))', 3857)) as result
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
        $this->assertEqualsWithDelta(445277.96, $vertex[0], 0.01);
        $this->assertEqualsWithDelta(445640.11, $vertex[1], 0.01);
    }
}
