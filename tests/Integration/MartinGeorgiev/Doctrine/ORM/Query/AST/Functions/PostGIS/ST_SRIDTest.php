<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SRID;
use PHPUnit\Framework\Attributes\Test;

final class ST_SRIDTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_SRID' => ST_SRID::class,
        ];
    }

    #[Test]
    public function returns_srid_of_geometry(): void
    {
        $dql = 'SELECT ST_SRID(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(4326, $result[0]['result']);
    }
}
