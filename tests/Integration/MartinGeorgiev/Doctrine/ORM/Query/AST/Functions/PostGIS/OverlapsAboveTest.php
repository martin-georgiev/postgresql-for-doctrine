<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\OverlapsAbove;
use PHPUnit\Framework\Attributes\Test;

final class OverlapsAboveTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVERLAPS_ABOVE' => OverlapsAbove::class,
        ];
    }

    #[Test]
    public function returns_whether_the_bounding_box_overlaps_above_from_entity_fields(): void
    {
        $dql = 'SELECT OVERLAPS_ABOVE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_whether_the_bounding_box_overlaps_above_from_a_literal_operand(): void
    {
        $dql = "SELECT OVERLAPS_ABOVE(g.geometry1, 'POINT(0 -1)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
