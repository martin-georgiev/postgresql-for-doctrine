<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineCrossingDirection;
use PHPUnit\Framework\Attributes\Test;

class ST_LineCrossingDirectionTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LINECROSSINGDIRECTION' => ST_LineCrossingDirection::class,
        ];
    }

    #[Test]
    public function returns_crossing_direction_when_lines_cross(): void
    {
        $dql = 'SELECT ST_LineCrossingDirection(\'LINESTRING(0 0, 2 2)\', \'LINESTRING(0 2, 2 0)\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsInt($result[0]['result']);
    }

    #[Test]
    public function returns_zero_when_lines_do_not_cross(): void
    {
        $dql = 'SELECT ST_LineCrossingDirection(\'LINESTRING(0 0, 1 1)\', \'LINESTRING(3 3, 4 4)\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function returns_crossing_direction_in_where_clause(): void
    {
        $dql = 'SELECT ST_LineCrossingDirection(\'LINESTRING(0 0, 2 2)\', \'LINESTRING(0 2, 2 0)\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE ST_LineCrossingDirection(\'LINESTRING(0 0, 2 2)\', \'LINESTRING(0 2, 2 0)\') > 0';

        $result = $this->executeDqlQuery($dql);
        $this->assertNotEmpty($result);
    }
}
