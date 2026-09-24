<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\OverlapsLeft;
use PHPUnit\Framework\Attributes\Test;

final class OverlapsLeftTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVERLAPS_LEFT' => OverlapsLeft::class,
        ];
    }

    #[Test]
    public function returns_whether_the_bounding_box_overlaps_left_from_entity_fields(): void
    {
        $dql = 'SELECT OVERLAPS_LEFT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_the_bounding_box_overlaps_left_from_a_literal_operand(): void
    {
        $dql = "SELECT OVERLAPS_LEFT(g.geometry1, 'SRID=4326;POINT(1 1)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
