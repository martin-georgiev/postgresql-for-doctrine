<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Y;
use PHPUnit\Framework\Attributes\Test;

final class ST_YTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_Y' => ST_Y::class,
        ];
    }

    #[Test]
    public function returns_the_y_coordinate_from_an_entity_field(): void
    {
        $dql = 'SELECT ST_Y(g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result']);
    }

    #[Test]
    public function returns_the_y_coordinate_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_Y('POINT(1 1)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result']);
    }
}
