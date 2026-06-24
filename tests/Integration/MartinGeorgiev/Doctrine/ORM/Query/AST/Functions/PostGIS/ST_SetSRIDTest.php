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
    public function sets_srid_on_geometry(): void
    {
        $dql = 'SELECT ST_SRID(ST_SETSRID(g.geometry1, 3857)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 4';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3857, $result[0]['result']);
    }

    #[Test]
    public function changes_srid_on_geometry_with_existing_srid(): void
    {
        $dql = 'SELECT ST_SRID(ST_SETSRID(g.geometry1, 3857)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3857, $result[0]['result']);
    }
}
