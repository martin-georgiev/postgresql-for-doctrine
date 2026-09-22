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
    public function returns_the_outer_ring_of_a_polygon(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_EXTERIORRING(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('LINESTRING(0 0,0 4,4 4,4 0,0 0)', $result[0]['result']);
    }

    #[Test]
    public function returns_null_for_linestring(): void
    {
        $dql = 'SELECT ST_EXTERIORRING(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
