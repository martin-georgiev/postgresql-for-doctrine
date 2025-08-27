<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SpatialContainedBy;
use PHPUnit\Framework\Attributes\Test;

class SpatialContainedByTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SPATIAL_CONTAINED_BY' => SpatialContainedBy::class,
        ];
    }

    #[Test]
    public function returns_false_with_overlapping_polygons(): void
    {
        $dql = 'SELECT SPATIAL_CONTAINED_BY(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_false_with_separate_points(): void
    {
        $dql = 'SELECT SPATIAL_CONTAINED_BY(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_comparing_identical_geometries(): void
    {
        $dql = 'SELECT SPATIAL_CONTAINED_BY(g.geometry1, g.geometry1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g 
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
