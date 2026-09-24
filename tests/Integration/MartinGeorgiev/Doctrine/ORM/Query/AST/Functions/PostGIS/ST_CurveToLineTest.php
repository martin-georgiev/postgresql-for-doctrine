<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CurveToLine;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use PHPUnit\Framework\Attributes\Test;

final class ST_CurveToLineTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_CURVETOLINE' => ST_CurveToLine::class,
            'ST_LENGTH' => ST_Length::class,
        ];
    }

    #[Test]
    public function converts_the_curve_to_a_line_from_an_entity_field(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_CURVETOLINE(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }

    #[Test]
    public function converts_the_curve_to_a_line_with_tolerance(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_CURVETOLINE(g.geometry1, 0.01, 1, 0)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }

    #[Test]
    public function converts_the_curve_to_a_line_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_LENGTH(ST_CURVETOLINE('LINESTRING(0 0, 1 1, 2 2)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }
}
