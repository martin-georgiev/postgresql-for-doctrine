<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ExteriorRing;
use PHPUnit\Framework\Attributes\Test;

final class ST_ExteriorRingTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASTEXT' => ST_AsText::class,
            'ST_EXTERIORRING' => ST_ExteriorRing::class,
        ];
    }

    #[Test]
    public function returns_the_outer_ring_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_ASTEXT(ST_EXTERIORRING('POLYGON((0 0,4 0,4 4,0 4,0 0))')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('LINESTRING(0 0,4 0,4 4,0 4,0 0)', $result[0]['result']);
    }

    #[Test]
    public function returns_the_outer_ring_from_an_entity_field(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_EXTERIORRING(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('LINESTRING(0 0,0 4,4 4,4 0,0 0)', $result[0]['result']);
    }
}
