<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineExtend;
use PHPUnit\Framework\Attributes\Test;

final class ST_LineExtendTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_LENGTH' => ST_Length::class,
            'ST_LINEEXTEND' => ST_LineExtend::class,
        ];
    }

    #[Test]
    public function returns_the_extended_form_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_LENGTH(ST_LINEEXTEND(ST_GEOMFROMTEXT('LINESTRING(0 0,4 0)'), 1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(5, $result[0]['result']);
    }

    #[Test]
    public function returns_the_extended_form_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_LINEEXTEND(g.geometry1, 0.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.3284271247461907, $result[0]['result'], 0.000000000000001);
    }

    #[Test]
    public function respects_the_backward_distance_argument(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_LINEEXTEND(g.geometry1, 0.5, 0.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.8284271247461907, $result[0]['result'], 0.000000000000001);
    }
}
