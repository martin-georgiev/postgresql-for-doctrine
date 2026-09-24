<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Difference;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NumInteriorRings;
use PHPUnit\Framework\Attributes\Test;

final class ST_NumInteriorRingsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_DIFFERENCE' => ST_Difference::class,
            'ST_NUMINTERIORRINGS' => ST_NumInteriorRings::class,
        ];
    }

    #[Test]
    public function returns_the_hole_count_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_NUMINTERIORRINGS('POLYGON((0 4,4 4,4 0,0 0,0 4),(3 1,3 3,1 3,1 1,3 1))') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function returns_the_hole_count_from_entity_fields(): void
    {
        $dql = 'SELECT ST_NUMINTERIORRINGS(ST_DIFFERENCE(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }
}
