<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CurveToLine;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use PHPUnit\Framework\Attributes\Test;

final class ST_CurveToLineTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_CURVETOLINE' => ST_CurveToLine::class,
            'ST_EQUALS' => ST_Equals::class,
            'ST_LENGTH' => ST_Length::class,
        ];
    }

    #[Test]
    public function converts_curve_to_line(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_CURVETOLINE(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }

    #[Test]
    public function converts_curve_with_tolerance_parameters(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_CURVETOLINE(g.geometry1, 0.01, 1, 0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }

    #[Test]
    public function applies_to_polygon_geometry(): void
    {
        $dql = 'SELECT ST_AREA(ST_CURVETOLINE(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }

    #[Test]
    public function applies_to_point_geometry(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_CURVETOLINE(g.geometry1), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
