<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Distance;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Translate;
use PHPUnit\Framework\Attributes\Test;

class ST_TranslateTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_DISTANCE' => ST_Distance::class,
            'ST_LENGTH' => ST_Length::class,
            'ST_TRANSLATE' => ST_Translate::class,
        ];
    }

    #[Test]
    public function translates_point_by_offset(): void
    {
        $dql = 'SELECT ST_DISTANCE(g.geometry1, ST_TRANSLATE(g.geometry1, 10.0, 10.0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(14.142135623730951, $result[0]['result'], 0.0000000000000001, 'ST_Translate should move point by expected distance');
    }

    #[Test]
    public function translates_polygon_by_offset(): void
    {
        $dql = 'SELECT ST_AREA(ST_TRANSLATE(g.geometry1, 5.0, 5.0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result'], 'ST_Translate should preserve polygon area');
    }

    #[Test]
    public function translates_linestring_by_offset(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_TRANSLATE(g.geometry1, 2.0, 2.0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.8284271247461903, $result[0]['result'], 0.0000000000000001, 'ST_Translate should preserve linestring length');
    }

    #[Test]
    public function translates_polygon_with_parameters(): void
    {
        $dql = 'SELECT ST_AREA(ST_TRANSLATE(g.geometry1, :dx, :dy)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql, ['dx' => 5.0, 'dy' => 5.0]);
        $this->assertEquals(16, $result[0]['result']);
    }

    #[Test]
    public function translates_polygon_with_function_expressions(): void
    {
        $dql = 'SELECT ST_AREA(ST_TRANSLATE(g.geometry1, ABS(5.0), ABS(5.0))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }
}
