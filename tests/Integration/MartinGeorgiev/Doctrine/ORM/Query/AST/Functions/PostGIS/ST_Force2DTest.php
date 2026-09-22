<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CoordDim;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Force2D;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use PHPUnit\Framework\Attributes\Test;

final class ST_Force2DTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_COORDDIM' => ST_CoordDim::class,
            'ST_FORCE2D' => ST_Force2D::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
        ];
    }

    #[Test]
    public function returns_the_2d_form_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_COORDDIM(ST_FORCE2D(ST_GEOMFROMTEXT('POINT Z(1 2 3)'))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2, $result[0]['result']);
    }

    #[Test]
    public function returns_the_2d_form_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_COORDDIM(ST_FORCE2D(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 11';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2, $result[0]['result']);
    }
}
