<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Union;
use PHPUnit\Framework\Attributes\Test;

final class ST_UnionTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_UNION' => ST_Union::class,
        ];
    }

    #[Test]
    public function returns_the_union_of_wkt_literals(): void
    {
        $dql = "SELECT ST_AREA(ST_UNION(ST_GEOMFROMTEXT('POLYGON((0 0,0 2,2 2,2 0,0 0))'), ST_GEOMFROMTEXT('POLYGON((1 1,1 3,3 3,3 1,1 1))'))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(7, $result[0]['result']);
    }

    #[Test]
    public function returns_the_union_of_entity_fields(): void
    {
        $dql = 'SELECT ST_AREA(ST_UNION(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 4';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(7, $result[0]['result']);
    }
}
