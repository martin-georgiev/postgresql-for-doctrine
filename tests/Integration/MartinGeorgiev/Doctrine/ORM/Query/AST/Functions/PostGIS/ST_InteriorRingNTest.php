<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Difference;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_InteriorRingN;
use PHPUnit\Framework\Attributes\Test;

final class ST_InteriorRingNTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASTEXT' => ST_AsText::class,
            'ST_DIFFERENCE' => ST_Difference::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_INTERIORRINGN' => ST_InteriorRingN::class,
        ];
    }

    #[Test]
    public function parses_the_nth_hole_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_ASTEXT(ST_INTERIORRINGN(ST_GEOMFROMTEXT('POLYGON((0 0,4 0,4 4,0 4,0 0),(1 1,2 1,2 2,1 2,1 1))'), 1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('LINESTRING(1 1,2 1,2 2,1 2,1 1)', $result[0]['result']);
    }

    #[Test]
    public function returns_the_nth_hole_cut_from_entity_fields(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_INTERIORRINGN(ST_DIFFERENCE(g.geometry1, g.geometry2), 1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('LINESTRING(3 1,3 3,1 3,1 1,3 1)', $result[0]['result']);
    }
}
