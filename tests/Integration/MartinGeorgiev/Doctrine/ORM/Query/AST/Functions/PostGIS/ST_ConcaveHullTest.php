<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ConcaveHull;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use PHPUnit\Framework\Attributes\Test;

final class ST_ConcaveHullTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_CONCAVEHULL' => ST_ConcaveHull::class,
            'ST_EQUALS' => ST_Equals::class,
        ];
    }

    #[Test]
    public function returns_concave_hull_equal_to_original_for_convex_polygon(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_CONCAVEHULL(g.geometry1, 1.0), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
