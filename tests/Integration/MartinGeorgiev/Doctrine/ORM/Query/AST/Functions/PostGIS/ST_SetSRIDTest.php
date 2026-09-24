<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SetSRID;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SRID;
use PHPUnit\Framework\Attributes\Test;

final class ST_SetSRIDTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_SETSRID' => ST_SetSRID::class,
            'ST_SRID' => ST_SRID::class,
        ];
    }

    #[Test]
    public function returns_the_geometry_with_the_srid_set_from_an_entity_field(): void
    {
        $dql = 'SELECT ST_SRID(ST_SETSRID(g.geometry1, 3857)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 4';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3857, $result[0]['result']);
    }
}
