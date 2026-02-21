<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use PHPUnit\Framework\Attributes\Test;

class ST_AreaTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
        ];
    }

    #[Test]
    public function returns_area_for_polygon(): void
    {
        $dql = 'SELECT ST_AREA(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_for_linestring(): void
    {
        $dql = 'SELECT ST_AREA(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_for_point(): void
    {
        $dql = 'SELECT ST_AREA(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function returns_area_for_geography_polygon(): void
    {
        $dql = "SELECT ST_AREA(g.geography1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(386273830.62023926, $result[0]['result'], 0.01);
    }

    #[Test]
    public function returns_area_for_geography_polygon_with_use_spheroid(): void
    {
        $dql = "SELECT ST_AREA(g.geography1, 'true') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(386273830.62023926, $result[0]['result'], 0.01);
    }
}
