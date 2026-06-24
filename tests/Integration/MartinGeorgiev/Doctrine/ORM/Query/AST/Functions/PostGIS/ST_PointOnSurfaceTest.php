<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Contains;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_PointOnSurface;
use PHPUnit\Framework\Attributes\Test;

final class ST_PointOnSurfaceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_CONTAINS' => ST_Contains::class,
            'ST_POINTONSURFACE' => ST_PointOnSurface::class,
        ];
    }

    #[Test]
    public function returns_point_inside_polygon(): void
    {
        $dql = 'SELECT ST_CONTAINS(g.geometry1, ST_POINTONSURFACE(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
