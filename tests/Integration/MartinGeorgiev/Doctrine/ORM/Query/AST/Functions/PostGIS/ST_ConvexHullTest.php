<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ConvexHull;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use PHPUnit\Framework\Attributes\Test;

final class ST_ConvexHullTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_CONVEXHULL' => ST_ConvexHull::class,
            'ST_EQUALS' => ST_Equals::class,
        ];
    }

    #[Test]
    public function returns_convex_hull_of_point(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_CONVEXHULL(g.geometry1), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
