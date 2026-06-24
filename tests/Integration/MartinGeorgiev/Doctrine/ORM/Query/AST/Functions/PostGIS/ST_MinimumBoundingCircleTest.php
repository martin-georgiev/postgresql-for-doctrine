<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MinimumBoundingCircle;
use PHPUnit\Framework\Attributes\Test;

final class ST_MinimumBoundingCircleTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_MINIMUMBOUNDINGCIRCLE' => ST_MinimumBoundingCircle::class,
        ];
    }

    #[Test]
    public function returns_bounding_circle_with_area_greater_than_polygon(): void
    {
        $dql = 'SELECT ST_AREA(ST_MINIMUMBOUNDINGCIRCLE(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertGreaterThan(16, $result[0]['result']);
    }
}
