<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineLocatePoint;
use PHPUnit\Framework\Attributes\Test;

final class ST_LineLocatePointTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_LINELOCATEPOINT' => ST_LineLocatePoint::class,
        ];
    }

    #[Test]
    public function returns_the_located_fraction_of_wkt_literals(): void
    {
        $dql = "SELECT ST_LINELOCATEPOINT(ST_GEOMFROMTEXT('LINESTRING(0 0,4 0)'), ST_GEOMFROMTEXT('POINT(2 0)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.5, $result[0]['result']);
    }

    #[Test]
    public function returns_the_located_fraction_of_entity_fields(): void
    {
        $dql = 'SELECT ST_LINELOCATEPOINT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 9';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.5, $result[0]['result']);
    }
}
