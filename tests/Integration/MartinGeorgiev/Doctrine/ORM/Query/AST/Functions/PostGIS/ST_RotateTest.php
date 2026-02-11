<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Distance;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Rotate;
use PHPUnit\Framework\Attributes\Test;

class ST_RotateTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_DISTANCE' => ST_Distance::class,
            'ST_LENGTH' => ST_Length::class,
            'ST_ROTATE' => ST_Rotate::class,
        ];
    }

    #[Test]
    public function rotates_point_around_origin(): void
    {
        $dql = 'SELECT ST_DISTANCE(ST_ROTATE(g.geometry1, 0.785398), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result'], 'should not move point at origin');
    }

    #[Test]
    public function rotates_polygon_around_origin(): void
    {
        $dql = 'SELECT ST_AREA(ST_ROTATE(g.geometry1, 1.570796)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(16, $result[0]['result'], 0.000000000000002, 'should preserve polygon area');
    }

    #[Test]
    public function rotates_linestring_around_origin(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_ROTATE(g.geometry1, 0.523599)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.8284271247461903, $result[0]['result'], 0.000000000000001, 'should preserve linestring length');
    }

    #[Test]
    public function rotates_polygon_with_parameter(): void
    {
        $dql = 'SELECT ST_AREA(ST_ROTATE(g.geometry1, :angle)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql, ['angle' => 1.570796]);
        $this->assertEqualsWithDelta(16, $result[0]['result'], 0.000000000000002);
    }

    #[Test]
    public function rotates_polygon_with_function_expression(): void
    {
        $dql = 'SELECT ST_AREA(ST_ROTATE(g.geometry1, ABS(1.570796))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(16, $result[0]['result'], 0.000000000000002);
    }
}
