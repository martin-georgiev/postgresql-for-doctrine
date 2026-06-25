<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Rotate;
use PHPUnit\Framework\Attributes\Test;

final class ST_RotateTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_EQUALS' => ST_Equals::class,
            'ST_GEOMETRYTYPE' => ST_GeometryType::class,
            'ST_ROTATE' => ST_Rotate::class,
        ];
    }

    #[Test]
    public function preserves_point_at_origin(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_ROTATE(g.geometry1, 0.785398), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function changes_polygon_when_rotated(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_ROTATE(g.geometry1, 1.570796), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function changes_linestring_when_rotated(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_ROTATE(g.geometry1, 0.523599), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function preserves_geometry_type_with_parameter(): void
    {
        $dql = 'SELECT ST_GEOMETRYTYPE(ST_ROTATE(g.geometry1, :angle)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql, ['angle' => 1.570796]);
        $this->assertEquals('ST_Polygon', $result[0]['result']);
    }

    #[Test]
    public function preserves_geometry_type_with_function_expression(): void
    {
        $dql = 'SELECT ST_GEOMETRYTYPE(ST_ROTATE(g.geometry1, ABS(1.570796))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_Polygon', $result[0]['result']);
    }

    #[Test]
    public function preserves_geometry_type_with_custom_origin(): void
    {
        $dql = 'SELECT ST_GEOMETRYTYPE(ST_ROTATE(g.geometry1, 1.570796, 21, 22)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_Polygon', $result[0]['result']);
    }
}
