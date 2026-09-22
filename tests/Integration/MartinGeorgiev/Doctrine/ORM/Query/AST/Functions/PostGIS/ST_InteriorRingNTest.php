<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Difference;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_InteriorRingN;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use PHPUnit\Framework\Attributes\Test;

final class ST_InteriorRingNTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_INTERIORRINGN' => ST_InteriorRingN::class,
            'ST_DIFFERENCE' => ST_Difference::class,
            'ST_LENGTH' => ST_Length::class,
        ];
    }

    #[Test]
    public function returns_the_hole_of_a_holed_polygon(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_INTERIORRINGN(ST_DIFFERENCE(g.geometry1, g.geometry2), 1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(8.0, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function returns_null_for_out_of_range_index(): void
    {
        $dql = 'SELECT ST_INTERIORRINGN(ST_DIFFERENCE(g.geometry1, g.geometry2), 2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }

    #[Test]
    public function returns_null_for_linestring(): void
    {
        $dql = 'SELECT ST_INTERIORRINGN(g.geometry1, 1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
