<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_FlipCoordinates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use PHPUnit\Framework\Attributes\Test;

final class ST_FlipCoordinatesTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASTEXT' => ST_AsText::class,
            'ST_FLIPCOORDINATES' => ST_FlipCoordinates::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
        ];
    }

    #[Test]
    public function returns_the_flipped_coordinates_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_ASTEXT(ST_FLIPCOORDINATES(ST_GEOMFROMTEXT('POINT(1 2)'))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT(2 1)', $result[0]['result']);
    }

    #[Test]
    public function returns_the_flipped_coordinates_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_FLIPCOORDINATES(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POLYGON((0 0,4 0,4 4,0 4,0 0))', $result[0]['result']);
    }
}
