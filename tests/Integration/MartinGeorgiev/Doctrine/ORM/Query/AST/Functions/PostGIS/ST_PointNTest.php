<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_PointN;
use PHPUnit\Framework\Attributes\Test;

final class ST_PointNTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASTEXT' => ST_AsText::class,
            'ST_POINTN' => ST_PointN::class,
        ];
    }

    #[Test]
    public function returns_nth_vertex_of_linestring(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_POINTN(g.geometry1, 2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT(1 1)', $result[0]['result']);
    }

    #[Test]
    public function returns_null_for_out_of_range_index(): void
    {
        $dql = 'SELECT ST_POINTN(g.geometry1, 10) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
