<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_RemoveSmallParts;
use PHPUnit\Framework\Attributes\Test;

final class ST_RemoveSmallPartsTest extends SpatialOperatorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgisVersion(30500, 'ST_RemoveSmallParts');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ST_REMOVESMALLPARTS' => ST_RemoveSmallParts::class,
            'ST_EQUALS' => ST_Equals::class,
        ];
    }

    #[Test]
    public function returns_the_geometry_without_small_parts_from_entity_fields(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_REMOVESMALLPARTS(g.geometry1, 1, 0), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_the_geometry_without_small_parts_from_a_literal_operand(): void
    {
        $dql = "SELECT ST_EQUALS(ST_REMOVESMALLPARTS(g.geometry1, 1, 0), 'SRID=4326;POLYGON((0 0, 0 4, 4 4, 4 0, 0 0))') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
