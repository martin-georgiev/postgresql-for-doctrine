<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_StartPoint;
use PHPUnit\Framework\Attributes\Test;

final class ST_StartPointTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASTEXT' => ST_AsText::class,
            'ST_STARTPOINT' => ST_StartPoint::class,
        ];
    }

    #[Test]
    public function returns_first_vertex_of_linestring(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_STARTPOINT(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT(0 0)', $result[0]['result']);
    }

    #[Test]
    public function returns_first_vertex_of_polygon_ring(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_STARTPOINT(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT(0 0)', $result[0]['result']);
    }
}
