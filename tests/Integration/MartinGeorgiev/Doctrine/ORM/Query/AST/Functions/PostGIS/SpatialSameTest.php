<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\SpatialSame;
use PHPUnit\Framework\Attributes\Test;

class SpatialSameTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SPATIAL_SAME' => SpatialSame::class,
        ];
    }

    #[Test]
    public function returns_false_when_geometries_are_different(): void
    {
        $dql = 'SELECT SPATIAL_SAME(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_geometries_are_identical(): void
    {
        $dql = 'SELECT SPATIAL_SAME(g.geometry1, g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_geometries_have_same_bounding_box(): void
    {
        $dql = 'SELECT SPATIAL_SAME(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
