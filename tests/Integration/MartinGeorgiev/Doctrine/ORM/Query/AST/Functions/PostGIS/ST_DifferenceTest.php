<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Difference;
use PHPUnit\Framework\Attributes\Test;

final class ST_DifferenceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_DIFFERENCE' => ST_Difference::class,
        ];
    }

    #[Test]
    public function returns_the_difference_from_entity_fields(): void
    {
        $dql = 'SELECT ST_AREA(ST_DIFFERENCE(g.geometry1, g.geometry2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(12, $result[0]['result']);
    }

    #[Test]
    public function returns_the_difference_from_a_literal_operand(): void
    {
        $dql = "SELECT ST_AREA(ST_DIFFERENCE(g.geometry1, 'SRID=4326;POLYGON((1 1, 1 3, 3 3, 3 1, 1 1))')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(12, $result[0]['result']);
    }
}
