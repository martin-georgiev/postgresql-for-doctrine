<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsGeoJSON;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromGeoJSON;
use PHPUnit\Framework\Attributes\Test;

final class ST_GeomFromGeoJSONTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASGEOJSON' => ST_AsGeoJSON::class,
            'ST_GEOMFROMGEOJSON' => ST_GeomFromGeoJSON::class,
        ];
    }

    #[Test]
    public function returns_the_geometry_of_a_geojson_literal(): void
    {
        $dql = 'SELECT ST_ASGEOJSON(ST_GEOMFROMGEOJSON(\'{"type":"Point","coordinates":[1,2]}\')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{"type":"Point","coordinates":[1,2]}', $result[0]['result']);
    }

    #[Test]
    public function returns_the_geometry_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_ASGEOJSON(ST_GEOMFROMGEOJSON(ST_ASGEOJSON(g.geometry1))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{"type":"Point","coordinates":[0,0]}', $result[0]['result']);
    }
}
