<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Simplify;
use PHPUnit\Framework\Attributes\Test;

class ST_SimplifyTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_EQUALS' => ST_Equals::class,
            'ST_LENGTH' => ST_Length::class,
            'ST_SIMPLIFY' => ST_Simplify::class,
        ];
    }

    #[Test]
    public function returns_simplified_linestring(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_SIMPLIFY(g.geometry1, 0.1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.8284271247461903, $result[0]['result'], 0.0000000000000001, 'should preserve length for straight linestrings');
    }

    #[Test]
    public function returns_simplified_polygon(): void
    {
        $dql = 'SELECT ST_AREA(ST_SIMPLIFY(g.geometry1, 0.2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result'], 'should preserve area for rectangular polygons');
    }

    #[Test]
    public function returns_original_point(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_SIMPLIFY(g.geometry1, 0.1), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'should return unchanged point for point geometries');
    }
}
