<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Collect;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryN;
use PHPUnit\Framework\Attributes\Test;

final class ST_GeometryNTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASTEXT' => ST_AsText::class,
            'ST_COLLECT' => ST_Collect::class,
            'ST_GEOMETRYN' => ST_GeometryN::class,
        ];
    }

    #[Test]
    public function returns_the_nth_element_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_ASTEXT(ST_GEOMETRYN('MULTILINESTRING((0 0,1 1,2 2),(3 3,4 4,5 5))', 1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('LINESTRING(0 0,1 1,2 2)', $result[0]['result']);
    }

    #[Test]
    public function returns_the_nth_element_from_entity_fields(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_GEOMETRYN(ST_COLLECT(g.geometry1, g.geometry2), 1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('LINESTRING(0 0,1 1,2 2)', $result[0]['result']);
    }
}
