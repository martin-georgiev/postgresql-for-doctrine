<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsGeoJSON;
use PHPUnit\Framework\Attributes\Test;

class ST_AsGeoJSONTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASGEOJSON' => ST_AsGeoJSON::class,
        ];
    }

    #[Test]
    public function returns_geojson_for_polygon(): void
    {
        $dql = 'SELECT ST_ASGEOJSON(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $geojson = \json_decode($result[0]['result'], true);
        $this->assertIsArray($geojson);
        $this->assertSame('Polygon', $geojson['type']);
        $this->assertIsArray($geojson['coordinates']);
    }

    #[Test]
    public function returns_geojson_for_point(): void
    {
        $dql = 'SELECT ST_ASGEOJSON(g.geography1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $geojson = \json_decode($result[0]['result'], true);
        $this->assertIsArray($geojson);
        $this->assertSame('Point', $geojson['type']);
        $this->assertSame([-9.1393, 38.7223], $geojson['coordinates']);
    }

    #[Test]
    public function respects_max_decimal_digits(): void
    {
        $dql = 'SELECT ST_ASGEOJSON(g.geography1, 2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $geojson = \json_decode($result[0]['result'], true);
        $this->assertIsArray($geojson);
        $this->assertSame('Point', $geojson['type']);
        // With 2 decimal digits, coordinates should be truncated
        $this->assertSame([-9.14, 38.72], $geojson['coordinates']);
    }

    #[Test]
    public function respects_options_parameter(): void
    {
        $dql = 'SELECT ST_ASGEOJSON(g.geometry1, 9, 1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $geojson = \json_decode($result[0]['result'], true);
        $this->assertIsArray($geojson);
        // Option 1 includes bounding box
        $this->assertArrayHasKey('bbox', $geojson);
    }
}
