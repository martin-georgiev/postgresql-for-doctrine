<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_X;
use PHPUnit\Framework\Attributes\Test;

final class ST_XTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_X' => ST_X::class,
        ];
    }

    #[Test]
    public function returns_the_x_coordinate_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_X(ST_GEOMFROMTEXT('POINT(3 4)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3, $result[0]['result']);
    }

    #[Test]
    public function returns_the_x_coordinate_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_X(g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result']);
    }
}
