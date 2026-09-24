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
    public function returns_the_rotated_geometry_from_entity_fields(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_ROTATE(g.geometry1, 0.785398), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
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

    #[Test]
    public function returns_the_rotated_geometry_from_wkt_literals(): void
    {
        $dql = "SELECT ST_EQUALS(ST_ROTATE('POINT(0 0)', 0.785398), 'POINT(0 0)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
