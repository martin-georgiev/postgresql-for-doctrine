<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\NDimensionalOverlaps;
use PHPUnit\Framework\Attributes\Test;

final class NDimensionalOverlapsTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ND_OVERLAPS' => NDimensionalOverlaps::class,
        ];
    }

    #[Test]
    public function returns_whether_the_n_dimensional_bounding_boxes_overlap_from_entity_fields(): void
    {
        $dql = 'SELECT ND_OVERLAPS(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_the_n_dimensional_bounding_boxes_overlap_from_a_literal_operand(): void
    {
        $dql = "SELECT ND_OVERLAPS(g.geometry1, 'SRID=4326;POLYGON((1 1, 1 3, 3 3, 3 1, 1 1))') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
