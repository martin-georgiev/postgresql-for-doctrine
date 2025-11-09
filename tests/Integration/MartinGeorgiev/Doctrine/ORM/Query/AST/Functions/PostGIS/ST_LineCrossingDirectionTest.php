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
    public function returns_zero_when_using_fixture_linestrings_that_do_not_cross(): void
    {
        $dql = 'SELECT ST_LINECROSSINGDIRECTION(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function returns_one_for_left_to_right_crossing(): void
    {
        $dql = 'SELECT ST_LINECROSSINGDIRECTION(\'LINESTRING(0 0, 2 2)\', \'LINESTRING(0 2, 2 0)\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result']);
    }

    #[Test]
    public function returns_negative_one_for_right_to_left_crossing(): void
    {
        $dql = 'SELECT ST_LINECROSSINGDIRECTION(\'LINESTRING(0 0, 2 2)\', \'LINESTRING(2 0, 0 2)\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(-1, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_when_lines_are_parallel(): void
    {
        $dql = 'SELECT ST_LINECROSSINGDIRECTION(\'LINESTRING(0 0, 2 0)\', \'LINESTRING(0 1, 2 1)\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_when_lines_touch_at_endpoint(): void
    {
        $dql = 'SELECT ST_LINECROSSINGDIRECTION(\'LINESTRING(0 0, 1 1)\', \'LINESTRING(1 1, 2 0)\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function can_filter_crossing_lines_in_where_clause(): void
    {
        $dql = 'SELECT ST_LINECROSSINGDIRECTION(\'LINESTRING(0 0, 2 2)\', \'LINESTRING(0 2, 2 0)\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE ST_LINECROSSINGDIRECTION(\'LINESTRING(0 0, 2 2)\', \'LINESTRING(0 2, 2 0)\') = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertCount(10, $result);
        $this->assertEquals(1, $result[0]['result']);
    }
}
