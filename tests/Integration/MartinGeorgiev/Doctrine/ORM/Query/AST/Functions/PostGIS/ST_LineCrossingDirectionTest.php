<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineCrossingDirection;
use PHPUnit\Framework\Attributes\Test;

final class ST_LineCrossingDirectionTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LINECROSSINGDIRECTION' => ST_LineCrossingDirection::class,
        ];
    }

    #[Test]
    public function returns_the_crossing_direction_from_entity_fields(): void
    {
        $dql = 'SELECT ST_LINECROSSINGDIRECTION(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_crossing_direction_from_a_literal_operand(): void
    {
        $dql = "SELECT ST_LINECROSSINGDIRECTION(g.geometry1, 'SRID=4326;LINESTRING(0 2, 2 0)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result']);
    }
}
