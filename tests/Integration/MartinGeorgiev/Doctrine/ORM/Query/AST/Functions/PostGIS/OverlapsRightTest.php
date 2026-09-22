<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\OverlapsRight;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use PHPUnit\Framework\Attributes\Test;

final class OverlapsRightTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVERLAPS_RIGHT' => OverlapsRight::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
        ];
    }

    #[Test]
    public function returns_whether_a_wkt_literal_overlaps_or_is_right_of_another(): void
    {
        $dql = "SELECT OVERLAPS_RIGHT(ST_GEOMFROMTEXT('POINT(1 1)'), ST_GEOMFROMTEXT('POINT(0 0)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_an_entity_field_overlaps_or_is_right_of_another(): void
    {
        $dql = 'SELECT OVERLAPS_RIGHT(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
