<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\SpatialSame;
use PHPUnit\Framework\Attributes\Test;

final class SpatialSameTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SPATIAL_SAME' => SpatialSame::class,
        ];
    }

    #[Test]
    public function returns_false_when_comparing_different_point_geometries(): void
    {
        $dql = 'SELECT SPATIAL_SAME(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
