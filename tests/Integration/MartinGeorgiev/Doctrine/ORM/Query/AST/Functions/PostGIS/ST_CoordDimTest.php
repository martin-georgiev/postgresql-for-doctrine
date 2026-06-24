<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CoordDim;
use PHPUnit\Framework\Attributes\Test;

final class ST_CoordDimTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_COORDDIM' => ST_CoordDim::class,
        ];
    }

    #[Test]
    public function returns_two_for_2d_point(): void
    {
        $dql = 'SELECT ST_COORDDIM(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2, $result[0]['result']);
    }

    #[Test]
    public function returns_three_for_3d_point(): void
    {
        $dql = 'SELECT ST_COORDDIM(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 11';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3, $result[0]['result']);
    }
}
