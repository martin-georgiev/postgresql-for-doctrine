<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NDimensionalOverlaps;
use PHPUnit\Framework\Attributes\Test;

class NDimensionalOverlapsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ND_OVERLAPS' => NDimensionalOverlaps::class,
        ];
    }

    #[Test]
    public function nd_overlaps_with_2d_geometries(): void
    {
        $dql = 'SELECT ND_OVERLAPS(g.geometry1, g.geometry2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'Overlapping polygons should return true for n-dimensional overlaps');
    }

    #[Test]
    public function nd_overlaps_with_identical_geometries(): void
    {
        $dql = 'SELECT ND_OVERLAPS(g.geometry1, g.geometry1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
