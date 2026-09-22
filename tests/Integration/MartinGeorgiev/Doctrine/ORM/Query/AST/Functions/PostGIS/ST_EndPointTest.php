<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_EndPoint;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_X;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Y;
use PHPUnit\Framework\Attributes\Test;

final class ST_EndPointTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ENDPOINT' => ST_EndPoint::class,
            'ST_X' => ST_X::class,
            'ST_Y' => ST_Y::class,
        ];
    }

    #[Test]
    public function returns_last_vertex_of_linestring(): void
    {
        $dql = 'SELECT ST_X(ST_ENDPOINT(g.geometry1)) as x, ST_Y(ST_ENDPOINT(g.geometry1)) as y
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2, $result[0]['x']);
        $this->assertEquals(2, $result[0]['y']);
    }

    #[Test]
    public function returns_null_for_polygon(): void
    {
        $dql = 'SELECT ST_ENDPOINT(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
