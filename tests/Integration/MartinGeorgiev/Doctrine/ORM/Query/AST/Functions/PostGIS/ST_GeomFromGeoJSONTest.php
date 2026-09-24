<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsGeoJSON;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromGeoJSON;
use PHPUnit\Framework\Attributes\Test;

final class ST_GeomFromGeoJSONTest extends SpatialOperatorTestCase
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
    public function roundtrips_a_geojson_literal(): void
    {
        $dql = "SELECT ST_EQUALS(g.geometry1, ST_GEOMFROMGEOJSON('{\"type\":\"Point\",\"coordinates\":[0,0]}')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function roundtrips_a_stored_geometry(): void
    {
        $dql = 'SELECT ST_EQUALS(g.geometry1, ST_GEOMFROMGEOJSON(ST_ASGEOJSON(g.geometry1))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
