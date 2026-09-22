<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ClosestPoint;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use PHPUnit\Framework\Attributes\Test;

final class ST_ClosestPointTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASTEXT' => ST_AsText::class,
            'ST_CLOSESTPOINT' => ST_ClosestPoint::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
        ];
    }

    #[Test]
    public function returns_the_closest_point_between_wkt_literals(): void
    {
        $dql = "SELECT ST_ASTEXT(ST_CLOSESTPOINT(ST_GEOMFROMTEXT('LINESTRING(0 0,4 0)'), ST_GEOMFROMTEXT('POINT(2 5)'))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT(2 0)', $result[0]['result']);
    }

    #[Test]
    public function returns_the_closest_point_between_entity_fields(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_CLOSESTPOINT(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 9';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT(2 2)', $result[0]['result']);
    }
}
