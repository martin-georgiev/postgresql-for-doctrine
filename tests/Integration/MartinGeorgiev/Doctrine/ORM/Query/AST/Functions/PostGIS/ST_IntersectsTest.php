<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Intersects;
use PHPUnit\Framework\Attributes\Test;

final class ST_IntersectsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_INTERSECTS' => ST_Intersects::class,
        ];
    }

    #[Test]
    public function returns_whether_wkt_literals_intersect(): void
    {
        $dql = "SELECT ST_INTERSECTS(ST_GEOMFROMTEXT('POLYGON((0 0,0 4,4 4,4 0,0 0))'), ST_GEOMFROMTEXT('POINT(2 2)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_entity_fields_intersect(): void
    {
        $dql = 'SELECT ST_INTERSECTS(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
