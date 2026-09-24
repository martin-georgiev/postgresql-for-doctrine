<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CurveN;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use PHPUnit\Framework\Attributes\Test;

final class ST_CurveNTest extends SpatialOperatorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgisVersion(30500, 'ST_CurveN');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ST_CURVEN' => ST_CurveN::class,
            'ST_LENGTH' => ST_Length::class,
        ];
    }

    #[Test]
    public function returns_first_curve_with_measurable_length(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_CURVEN(g.geometry1, 1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 14';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.4142135623730951, $result[0]['result']);
    }

    #[Test]
    public function returns_first_curve_with_measurable_length_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_LENGTH(ST_CURVEN('COMPOUNDCURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1), (3 1, 4 0))', 1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 14";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.4142135623730951, $result[0]['result']);
    }
}
