<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Point;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SRID;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_X;
use PHPUnit\Framework\Attributes\Test;

final class ST_PointTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_POINT' => ST_Point::class,
            'ST_SRID' => ST_SRID::class,
            'ST_X' => ST_X::class,
        ];
    }

    #[Test]
    public function creates_point_with_correct_x(): void
    {
        $dql = 'SELECT ST_X(ST_POINT(42, 7)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(42, $result[0]['result']);
    }

    #[Test]
    public function creates_point_with_srid(): void
    {
        $dql = 'SELECT ST_SRID(ST_POINT(1, 2, 4326)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(4326, $result[0]['result']);
    }
}
