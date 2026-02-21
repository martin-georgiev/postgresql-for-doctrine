<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Buffer;
use PHPUnit\Framework\Attributes\Test;

class ST_BufferTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_BUFFER' => ST_Buffer::class,
        ];
    }

    #[Test]
    public function returns_buffered_point(): void
    {
        $dql = 'SELECT ST_AREA(ST_BUFFER(g.geometry1, 1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.121445152258052, $result[0]['result'], 0.0000000000000001, 'Buffer of radius 1 around a point shall create a polygon approximation of a circle');
    }

    #[Test]
    public function returns_buffered_polygon(): void
    {
        $dql = 'SELECT ST_AREA(ST_BUFFER(g.geometry1, 0.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(24.780361288064512, $result[0]['result'], 0.0000000000000001, 'Buffer of 0.5 around 4x4 polygon increases area from 16 to approximately 24.78');
    }

    #[Test]
    public function returns_buffered_linestring(): void
    {
        $dql = 'SELECT ST_AREA(ST_BUFFER(g.geometry1, 0.2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.2562286559887985, $result[0]['result'], 0.0000000000000001, 'Buffer of 0.2 around LINESTRING(0 0, 1 1, 2 2) creates a polygon with specific area');
    }

    #[Test]
    public function returns_buffered_point_with_parameter(): void
    {
        $dql = 'SELECT ST_AREA(ST_BUFFER(g.geometry1, :radius)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql, ['radius' => 1]);
        $this->assertEqualsWithDelta(3.121445152258052, $result[0]['result'], 0.0000000000000001);
    }

    #[Test]
    public function returns_buffered_point_with_function_expression(): void
    {
        $dql = 'SELECT ST_AREA(ST_BUFFER(g.geometry1, ABS(1))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.121445152258052, $result[0]['result'], 0.0000000000000001);
    }

    #[Test]
    public function returns_buffered_point_with_quad_segs_parameter(): void
    {
        $dql = 'SELECT ST_AREA(ST_BUFFER(g.geometry1, 1, 32)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.1403311569547543, $result[0]['result'], 0.0000000000000001);
    }

    #[Test]
    public function returns_buffered_point_with_buffer_style_parameter(): void
    {
        $dql = "SELECT ST_AREA(ST_BUFFER(g.geometry1, 1, 'quad_segs=8')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.121445152258052, $result[0]['result'], 0.0000000000000001);
    }
}
