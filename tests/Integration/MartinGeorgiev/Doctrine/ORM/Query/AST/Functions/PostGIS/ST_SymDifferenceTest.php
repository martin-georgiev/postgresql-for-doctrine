<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SymDifference;
use PHPUnit\Framework\Attributes\Test;

class ST_SymDifferenceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_LENGTH' => ST_Length::class,
            'ST_SYMDIFFERENCE' => ST_SymDifference::class,
        ];
    }

    #[Test]
    public function returns_symmetric_difference_between_polygons(): void
    {
        $dql = 'SELECT ST_AREA(ST_SYMDIFFERENCE(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(12, $result[0]['result'], 'should calculate correct area: parts of each polygon not in the other');
    }

    #[Test]
    public function returns_symmetric_difference_between_other_polygons(): void
    {
        $dql = 'SELECT ST_AREA(ST_SYMDIFFERENCE(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 4';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(6, $result[0]['result'], 'should calculate correct exclusive area for overlapping polygons');
    }

    #[Test]
    public function returns_symmetric_difference_between_linestrings(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_SYMDIFFERENCE(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(5.656854249492381, $result[0]['result'], 0.000000000000001, 'should preserve total length of disjoint linestrings');
    }
}
