<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Collect;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use PHPUnit\Framework\Attributes\Test;

class ST_CollectTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_COLLECT' => ST_Collect::class,
            'ST_LENGTH' => ST_Length::class,
        ];
    }

    #[Test]
    public function collects_two_points_into_multipoint(): void
    {
        $dql = 'SELECT ST_AREA(ST_COLLECT(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result'], 'should return zero area for collected points');
    }

    #[Test]
    public function collects_two_polygons_into_multipolygon(): void
    {
        $dql = 'SELECT ST_AREA(ST_COLLECT(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(20, $result[0]['result'], 'should sum areas of collected polygons');
    }

    #[Test]
    public function collects_two_linestrings_into_multilinestring(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_COLLECT(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(5.656854249492381, $result[0]['result'], 0.0000000000000001, 'should sum lengths of collected linestrings');
    }

    #[Test]
    public function collects_mixed_geometry_types(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_COLLECT(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 5';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(8.48528137423857, $result[0]['result'], 0.000000000000001, 'should preserve linestring length in mixed geometry collection');
    }
}
