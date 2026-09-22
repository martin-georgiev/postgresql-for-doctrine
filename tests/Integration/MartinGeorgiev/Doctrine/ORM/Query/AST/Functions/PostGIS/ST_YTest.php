<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Y;
use PHPUnit\Framework\Attributes\Test;

final class ST_YTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_Y' => ST_Y::class,
        ];
    }

    #[Test]
    public function returns_the_y_coordinate_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_Y(ST_GEOMFROMTEXT('POINT(3 4)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(4, $result[0]['result']);
    }

    #[Test]
    public function returns_the_y_coordinate_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_Y(g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result']);
    }
}
