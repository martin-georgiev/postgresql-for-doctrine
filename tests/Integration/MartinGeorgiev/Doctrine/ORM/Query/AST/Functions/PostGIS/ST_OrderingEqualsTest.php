<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_OrderingEquals;
use PHPUnit\Framework\Attributes\Test;

final class ST_OrderingEqualsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_ORDERINGEQUALS' => ST_OrderingEquals::class,
        ];
    }

    #[Test]
    public function returns_whether_wkt_literals_are_ordering_equal(): void
    {
        $dql = "SELECT ST_ORDERINGEQUALS(ST_GEOMFROMTEXT('LINESTRING(0 0,1 1,2 2)'), ST_GEOMFROMTEXT('LINESTRING(0 0,1 1,2 2)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_entity_fields_are_ordering_equal(): void
    {
        $dql = 'SELECT ST_ORDERINGEQUALS(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 7';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
