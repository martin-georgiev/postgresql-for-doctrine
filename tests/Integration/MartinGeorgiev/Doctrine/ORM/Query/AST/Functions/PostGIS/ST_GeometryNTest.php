<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Collect;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryN;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_StartPoint;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_X;
use PHPUnit\Framework\Attributes\Test;

final class ST_GeometryNTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMETRYN' => ST_GeometryN::class,
            'ST_COLLECT' => ST_Collect::class,
            'ST_STARTPOINT' => ST_StartPoint::class,
            'ST_X' => ST_X::class,
        ];
    }

    #[Test]
    public function returns_first_element_of_a_collection(): void
    {
        $dql = 'SELECT ST_X(ST_STARTPOINT(ST_GEOMETRYN(ST_COLLECT(g.geometry1, g.geometry2), 1))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function returns_null_for_out_of_range_index(): void
    {
        $dql = 'SELECT ST_GEOMETRYN(ST_COLLECT(g.geometry1, g.geometry2), 10) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
