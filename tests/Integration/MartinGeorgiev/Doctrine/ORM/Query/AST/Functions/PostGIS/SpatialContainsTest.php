<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\SpatialContains;
use PHPUnit\Framework\Attributes\Test;

class SpatialContainsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SPATIAL_CONTAINS' => SpatialContains::class,
        ];
    }

    #[Test]
    public function returns_false_when_geometry_does_not_contain_another(): void
    {
        $dql = 'SELECT SPATIAL_CONTAINS(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_geometry_contains_another(): void
    {
        $dql = 'SELECT SPATIAL_CONTAINS(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_geometries_are_identical(): void
    {
        $dql = 'SELECT SPATIAL_CONTAINS(g.geometry1, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
