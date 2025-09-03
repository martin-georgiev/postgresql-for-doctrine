<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Difference;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use PHPUnit\Framework\Attributes\Test;

class ST_DifferenceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_DIFFERENCE' => ST_Difference::class,
            'ST_LENGTH' => ST_Length::class,
        ];
    }

    #[Test]
    public function returns_difference_between_polygons(): void
    {
        $dql = 'SELECT ST_AREA(ST_DIFFERENCE(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(12, $result[0]['result'], 'should calculate correct  difference area: outer polygon minus inner polygon');
    }

    #[Test]
    public function returns_difference_between_other_polygons(): void
    {
        $dql = 'SELECT ST_AREA(ST_DIFFERENCE(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 4';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3, $result[0]['result'], 'should calculate correct  difference area: smaller polygon minus overlapping larger polygon');
    }

    #[Test]
    public function returns_difference_between_linestrings(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_DIFFERENCE(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.8284271247461903, $result[0]['result'], 0.0000000000000001, 'should preserve original linestring length when geometries do not overlap');
    }
}
