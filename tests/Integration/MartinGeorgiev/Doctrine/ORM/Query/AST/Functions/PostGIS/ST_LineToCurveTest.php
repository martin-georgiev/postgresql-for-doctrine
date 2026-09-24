<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineToCurve;
use PHPUnit\Framework\Attributes\Test;

final class ST_LineToCurveTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LENGTH' => ST_Length::class,
            'ST_LINETOCURVE' => ST_LineToCurve::class,
        ];
    }

    #[Test]
    public function converts_line_to_curve(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_LINETOCURVE(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }

    #[Test]
    public function converts_line_to_curve_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_LENGTH(ST_LINETOCURVE('LINESTRING(0 0, 1 1, 2 2)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.8284271247461903, $result[0]['result']);
    }
}
