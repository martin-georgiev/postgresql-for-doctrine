<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_HasZ;
use PHPUnit\Framework\Attributes\Test;

class ST_HasZTest extends SpatialOperatorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgisVersion(30500, 'ST_HasZ');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ST_HASZ' => ST_HasZ::class,
        ];
    }

    #[Test]
    public function returns_false_for_2d_geometry(): void
    {
        $dql = 'SELECT ST_HASZ(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result'], '2D point should not have Z coordinate');
    }

    #[Test]
    public function returns_true_for_3d_geometry_with_z(): void
    {
        // id=11 contains POINT Z(0 0 5) - a 3D point with Z coordinate
        $dql = 'SELECT ST_HASZ(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 11';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], '3D point with Z coordinate should return true');
    }
}
