<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CoordDim;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Force3D;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Z;
use PHPUnit\Framework\Attributes\Test;

final class ST_Force3DTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_COORDDIM' => ST_CoordDim::class,
            'ST_FORCE3D' => ST_Force3D::class,
            'ST_Z' => ST_Z::class,
        ];
    }

    #[Test]
    public function preserves_3d_point(): void
    {
        $dql = 'SELECT ST_COORDDIM(g.geometry1) as original,
                       ST_COORDDIM(ST_FORCE2D(g.geometry1)) as transformed
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 11';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3, $result[0]['original']);
        $this->assertEquals(3, $result[0]['transformed']);
    }

    #[Test]
    public function promotes_2d_point_to_3d(): void
    {
        $dql = 'SELECT ST_COORDDIM(g.geometry1) as original,
                       ST_COORDDIM(ST_FORCE2D(g.geometry1)) as transformed
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2, $result[0]['original']);
        $this->assertEquals(3, $result[0]['transformed']);
    }
}
