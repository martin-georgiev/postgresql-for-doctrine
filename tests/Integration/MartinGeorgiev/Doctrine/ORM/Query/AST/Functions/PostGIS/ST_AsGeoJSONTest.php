<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsGeoJSON;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use PHPUnit\Framework\Attributes\Test;

final class ST_AsGeoJSONTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASGEOJSON' => ST_AsGeoJSON::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
        ];
    }

    #[Test]
    public function returns_the_geojson_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_ASGEOJSON(ST_GEOMFROMTEXT('POINT(1 2)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{"type":"Point","coordinates":[1,2]}', $result[0]['result']);
    }

    #[Test]
    public function returns_the_geojson_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_ASGEOJSON(g.geography1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{"type":"Point","coordinates":[-9.1393,38.7223]}', $result[0]['result']);
    }

    #[Test]
    public function respects_max_decimal_digits(): void
    {
        $dql = 'SELECT ST_ASGEOJSON(g.geography1, 2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{"type":"Point","coordinates":[-9.14,38.72]}', $result[0]['result']);
    }

    #[Test]
    public function respects_the_options_flag(): void
    {
        $dql = 'SELECT ST_ASGEOJSON(g.geometry1, 9, 1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{"type":"Polygon","bbox":[0.000000000,0.000000000,4.000000000,4.000000000],"coordinates":[[[0,0],[0,4],[4,4],[4,0],[0,0]]]}', $result[0]['result']);
    }
}
