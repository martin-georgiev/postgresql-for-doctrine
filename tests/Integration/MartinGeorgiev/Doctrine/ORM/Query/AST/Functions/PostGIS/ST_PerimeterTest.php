<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Perimeter;
use PHPUnit\Framework\Attributes\Test;

class ST_PerimeterTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_PERIMETER' => ST_Perimeter::class,
        ];
    }

    #[Test]
    public function returns_perimeter_for_polygon(): void
    {
        $dql = 'SELECT ST_PERIMETER(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }

    #[Test]
    public function returns_perimeter_for_smaller_polygon(): void
    {
        $dql = 'SELECT ST_PERIMETER(g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(8, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_for_point(): void
    {
        $dql = 'SELECT ST_PERIMETER(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_for_linestring(): void
    {
        $dql = 'SELECT ST_PERIMETER(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result'], 'PostGIS behavior is that perimeter is only defined for areal geometries');
    }

    #[Test]
    public function returns_perimeter_for_non_srid_polygon(): void
    {
        $dql = 'SELECT ST_PERIMETER(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 4';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(8, $result[0]['result']);
    }
}
