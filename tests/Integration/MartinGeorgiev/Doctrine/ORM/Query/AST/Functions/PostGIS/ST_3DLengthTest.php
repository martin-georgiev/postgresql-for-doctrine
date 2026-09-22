<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DLength;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use PHPUnit\Framework\Attributes\Test;

final class ST_3DLengthTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_3DLENGTH' => ST_3DLength::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
        ];
    }

    #[Test]
    public function returns_the_3d_length_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_3DLENGTH(ST_GEOMFROMTEXT('LINESTRING Z(0 0 0,0 3 4)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(5, $result[0]['result']);
    }

    #[Test]
    public function returns_the_3d_length_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_3DLENGTH(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 10';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2000, $result[0]['result']);
    }
}
