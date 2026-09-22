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
            'ST_NUMINTERIORRINGS' => ST_NumInteriorRings::class,
            'ST_DIFFERENCE' => ST_Difference::class,
        ];
    }

    #[Test]
    public function returns_one_for_polygon_with_a_single_hole(): void
    {
        $dql = 'SELECT ST_NUMINTERIORRINGS(ST_DIFFERENCE(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function returns_null_for_linestring(): void
    {
        $dql = 'SELECT ST_NUMINTERIORRINGS(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
