<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_HasM;
use PHPUnit\Framework\Attributes\Test;

class ST_HasMTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_HASM' => ST_HasM::class,
        ];
    }

    #[Test]
    public function returns_false_for_2d_geometry(): void
    {
        $dql = 'SELECT ST_HASM(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result'], '2D point should not have M coordinate');
    }

    #[Test]
    public function returns_true_for_geometry_with_m(): void
    {
        // id=12 contains POINT M(0 0 5) - a point with M coordinate
        $dql = 'SELECT ST_HASM(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 12';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'point with M coordinate should return true');
    }
}
