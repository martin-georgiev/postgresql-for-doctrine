<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length2D;
use PHPUnit\Framework\Attributes\Test;

class ST_Length2DTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LENGTH2D' => ST_Length2D::class,
        ];
    }

    #[Test]
    public function returns_length_for_linestring(): void
    {
        $dql = 'SELECT ST_LENGTH2D(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.8284271247461903, $result[0]['result'], 0.000000000000001, 'should return correct linestring length');
    }

    #[Test]
    public function returns_zero_for_polygon(): void
    {
        $dql = 'SELECT ST_LENGTH2D(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result'], 'should return zero for polygon length (not a perimeter)');
    }

    #[Test]
    public function returns_zero_for_point(): void
    {
        $dql = 'SELECT ST_LENGTH2D(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }
}
