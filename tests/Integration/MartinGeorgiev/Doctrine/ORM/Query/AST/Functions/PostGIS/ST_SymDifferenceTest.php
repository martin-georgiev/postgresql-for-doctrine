<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SymDifference;
use PHPUnit\Framework\Attributes\Test;

final class ST_SymDifferenceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
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
        $this->assertEquals(12, $result[0]['result']);
    }
}
