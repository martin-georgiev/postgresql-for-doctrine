<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MakePoint;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_X;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Z;
use PHPUnit\Framework\Attributes\Test;

final class ST_MakePointTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_MAKEPOINT' => ST_MakePoint::class,
            'ST_X' => ST_X::class,
            'ST_Z' => ST_Z::class,
        ];
    }

    #[Test]
    public function creates_2d_point(): void
    {
        $dql = 'SELECT ST_X(ST_MAKEPOINT(10, 20)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(10, $result[0]['result']);
    }

    #[Test]
    public function creates_3d_point(): void
    {
        $dql = 'SELECT ST_Z(ST_MAKEPOINT(11, 22, 33)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(33, $result[0]['result']);
    }
}
