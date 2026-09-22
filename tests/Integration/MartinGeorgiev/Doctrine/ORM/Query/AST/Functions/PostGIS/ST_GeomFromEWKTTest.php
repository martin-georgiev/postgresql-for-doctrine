<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsEWKT;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromEWKT;
use PHPUnit\Framework\Attributes\Test;

final class ST_GeomFromEWKTTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASEWKT' => ST_AsEWKT::class,
            'ST_GEOMFROMEWKT' => ST_GeomFromEWKT::class,
        ];
    }

    #[Test]
    public function returns_the_geometry_of_an_ewkt_literal(): void
    {
        $dql = "SELECT ST_ASEWKT(ST_GEOMFROMEWKT('SRID=4326;POINT(1 2)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('SRID=4326;POINT(1 2)', $result[0]['result']);
    }

    #[Test]
    public function returns_the_geometry_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_ASEWKT(ST_GEOMFROMEWKT(ST_ASEWKT(g.geometry1))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('SRID=4326;POLYGON((0 0,0 4,4 4,4 0,0 0))', $result[0]['result']);
    }
}
