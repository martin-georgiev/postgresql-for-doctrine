<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ClosestPoint;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryType;
use PHPUnit\Framework\Attributes\Test;

final class ST_ClosestPointTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_CLOSESTPOINT' => ST_ClosestPoint::class,
            'ST_GEOMETRYTYPE' => ST_GeometryType::class,
        ];
    }

    #[Test]
    public function returns_point_geometry(): void
    {
        $dql = 'SELECT ST_GEOMETRYTYPE(ST_CLOSESTPOINT(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_Point', $result[0]['result']);
    }
}
