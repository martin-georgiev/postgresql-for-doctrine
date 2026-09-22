<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsEWKT;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromEWKT;
use PHPUnit\Framework\Attributes\Test;

final class ST_GeomFromEWKTTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMEWKT' => ST_GeomFromEWKT::class,
            'ST_ASEWKT' => ST_AsEWKT::class,
            'ST_ASTEXT' => ST_AsText::class,
        ];
    }

    #[Test]
    public function creates_geometry_from_ewkt(): void
    {
        $dql = "SELECT ST_ASEWKT(ST_GEOMFROMEWKT('SRID=4326;POINT(1 2)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('SRID=4326;POINT(1 2)', $result[0]['result']);
    }

    #[Test]
    public function drops_the_srid_prefix_from_the_wkt_output(): void
    {
        $dql = "SELECT ST_ASTEXT(ST_GEOMFROMEWKT('SRID=4326;POINT(1 2)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT(1 2)', $result[0]['result']);
    }

    #[Test]
    public function creates_geometry_from_parameter(): void
    {
        $dql = 'SELECT ST_ASEWKT(ST_GEOMFROMEWKT(:ewkt)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql, [
            'ewkt' => 'SRID=3857;LINESTRING(0 0, 1000 0, 1000 1000)',
        ]);
        $this->assertSame('SRID=3857;LINESTRING(0 0,1000 0,1000 1000)', $result[0]['result']);
    }

    #[Test]
    public function round_trips_a_stored_geometry(): void
    {
        $dql = 'SELECT ST_ASEWKT(ST_GEOMFROMEWKT(ST_ASEWKT(g.geometry1))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('SRID=4326;POLYGON((0 0,0 4,4 4,4 0,0 0))', $result[0]['result']);
    }
}
