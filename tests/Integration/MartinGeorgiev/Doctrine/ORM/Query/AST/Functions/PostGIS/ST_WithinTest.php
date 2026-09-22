<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Within;
use PHPUnit\Framework\Attributes\Test;

final class ST_WithinTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_WITHIN' => ST_Within::class,
        ];
    }

    #[Test]
    public function returns_whether_a_wkt_literal_is_within_another(): void
    {
        $dql = "SELECT ST_WITHIN(ST_GEOMFROMTEXT('POINT(2 2)'), ST_GEOMFROMTEXT('POLYGON((0 0,0 4,4 4,4 0,0 0))')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_an_entity_field_is_within_another(): void
    {
        $dql = 'SELECT ST_WITHIN(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 6';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
