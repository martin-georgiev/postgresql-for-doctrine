<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsEWKT;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use PHPUnit\Framework\Attributes\Test;

final class ST_GeomFromTextTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_ASTEXT' => ST_AsText::class,
            'ST_ASEWKT' => ST_AsEWKT::class,
        ];
    }

    #[Test]
    public function creates_geometry_from_wkt(): void
    {
        $dql = "SELECT ST_ASTEXT(ST_GEOMFROMTEXT('POINT(1 2)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT(1 2)', $result[0]['result']);
    }

    #[Test]
    public function creates_geometry_without_a_spatial_reference_system(): void
    {
        $dql = "SELECT ST_ASEWKT(ST_GEOMFROMTEXT('LINESTRING(0 0, 1 1, 2 2)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('LINESTRING(0 0,1 1,2 2)', $result[0]['result']);
    }

    #[Test]
    public function creates_geometry_with_the_given_srid(): void
    {
        $dql = "SELECT ST_ASEWKT(ST_GEOMFROMTEXT('POINT(1 2)', 4326)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('SRID=4326;POINT(1 2)', $result[0]['result']);
    }

    #[Test]
    public function creates_geometry_from_parameter(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_GEOMFROMTEXT(:wkt)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql, [
            'wkt' => 'POLYGON((0 0, 0 4, 4 4, 4 0, 0 0))',
        ]);
        $this->assertSame('POLYGON((0 0,0 4,4 4,4 0,0 0))', $result[0]['result']);
    }

    #[Test]
    public function round_trips_a_stored_geometry(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_GEOMFROMTEXT(ST_ASTEXT(g.geometry1))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POLYGON((0 0,0 4,4 4,4 0,0 0))', $result[0]['result']);
    }
}
