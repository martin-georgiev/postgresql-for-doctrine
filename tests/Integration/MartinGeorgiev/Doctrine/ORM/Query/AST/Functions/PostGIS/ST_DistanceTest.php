<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Distance;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use PHPUnit\Framework\Attributes\Test;

final class ST_DistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_DISTANCE' => ST_Distance::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
        ];
    }

    #[Test]
    public function returns_the_distance_between_wkt_literals(): void
    {
        $dql = "SELECT ST_DISTANCE(ST_GEOMFROMTEXT('POINT(0 0)'), ST_GEOMFROMTEXT('POINT(3 4)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(5, $result[0]['result']);
    }

    #[Test]
    public function returns_the_distance_between_entity_fields(): void
    {
        $dql = 'SELECT ST_DISTANCE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.4142135623730951, $result[0]['result']);
    }

    #[Test]
    public function respects_the_use_spheroid_flag(): void
    {
        $dql = "SELECT ST_DISTANCE(g.geography1, g.geography2, 'true') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1585162.73961816, $result[0]['result'], 0.00000001);
    }
}
