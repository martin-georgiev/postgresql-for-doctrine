<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Scale;
use PHPUnit\Framework\Attributes\Test;

class ST_ScaleTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_EQUALS' => ST_Equals::class,
            'ST_LENGTH' => ST_Length::class,
            'ST_SCALE' => ST_Scale::class,
        ];
    }

    #[Test]
    public function scales_point_by_factors(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_SCALE(g.geometry1, 2.0, 2.0), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'should return unchanged point at origin (0 * factor = 0)');
    }

    #[Test]
    public function scales_polygon_by_factors(): void
    {
        $dql = 'SELECT ST_AREA(ST_SCALE(g.geometry1, 1.5, 1.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(36, $result[0]['result'], 'should scale polygon area by factor squared (16 * 1.5 * 1.5 = 36)');
    }

    #[Test]
    public function scales_linestring_by_factors(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_SCALE(g.geometry1, 0.5, 0.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.4142135623730951, $result[0]['result'], 0.000000000000001, 'should scale linestring length by factor (2.828... * 0.5 = 1.414...)');
    }
}
