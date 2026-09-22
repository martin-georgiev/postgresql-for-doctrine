<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_PointN;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_X;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Y;
use PHPUnit\Framework\Attributes\Test;

final class ST_PointNTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_POINTN' => ST_PointN::class,
            'ST_X' => ST_X::class,
            'ST_Y' => ST_Y::class,
        ];
    }

    #[Test]
    public function returns_middle_vertex_of_linestring(): void
    {
        $dql = 'SELECT ST_X(ST_POINTN(g.geometry1, 2)) as x, ST_Y(ST_POINTN(g.geometry1, 2)) as y
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.0, $result[0]['x'], 0.0001);
        $this->assertEqualsWithDelta(1.0, $result[0]['y'], 0.0001);
    }

    #[Test]
    public function returns_null_for_out_of_range_index(): void
    {
        $dql = 'SELECT ST_POINTN(g.geometry1, 10) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
