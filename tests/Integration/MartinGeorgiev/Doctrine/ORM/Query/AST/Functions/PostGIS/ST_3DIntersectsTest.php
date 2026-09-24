<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DIntersects;
use PHPUnit\Framework\Attributes\Test;

final class ST_3DIntersectsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_3DINTERSECTS' => ST_3DIntersects::class,
        ];
    }

    #[Test]
    public function returns_false_when_comparing_separate_point_geometries(): void
    {
        $dql = 'SELECT ST_3DINTERSECTS(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_comparing_separate_point_geometries_from_wkt_literals(): void
    {
        $dql = "SELECT ST_3DINTERSECTS('POINT(0 0)', 'POINT(1 1)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
