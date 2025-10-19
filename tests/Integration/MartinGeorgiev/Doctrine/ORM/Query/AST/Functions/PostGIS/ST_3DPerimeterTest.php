<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DPerimeter;
use PHPUnit\Framework\Attributes\Test;

class ST_3DPerimeterTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_3DPERIMETER' => ST_3DPerimeter::class,
        ];
    }

    #[Test]
    public function returns_3d_perimeter_for_polygon(): void
    {
        $dql = 'SELECT ST_3DPERIMETER(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_for_point(): void
    {
        $dql = 'SELECT ST_3DPERIMETER(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }
}
