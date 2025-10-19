<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DDistance;
use PHPUnit\Framework\Attributes\Test;

class ST_3DDistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_3DDISTANCE' => ST_3DDistance::class,
        ];
    }

    #[Test]
    public function returns_3d_distance_between_points(): void
    {
        $dql = 'SELECT ST_3DDISTANCE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.4142135623730951, $result[0]['result'], 0.0000000000000001);
    }

    #[Test]
    public function returns_3d_distance_between_polygons(): void
    {
        $dql = 'SELECT ST_3DDISTANCE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function returns_3d_distance_between_linestrings(): void
    {
        $dql = 'SELECT ST_3DDISTANCE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.4142135623730951, $result[0]['result'], 0.0000000000000001);
    }
}
