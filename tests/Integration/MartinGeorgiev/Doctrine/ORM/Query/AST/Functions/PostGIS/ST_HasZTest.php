<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_HasZ;
use PHPUnit\Framework\Attributes\Test;

final class ST_HasZTest extends SpatialOperatorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgisVersion(30500, 'ST_HasZ');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ST_HASZ' => ST_HasZ::class,
        ];
    }

    #[Test]
    public function returns_whether_the_geometry_has_a_z_coordinate_from_an_entity_field(): void
    {
        $dql = 'SELECT ST_HASZ(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_whether_the_geometry_has_a_z_coordinate_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_HASZ('POINT(0 0)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
