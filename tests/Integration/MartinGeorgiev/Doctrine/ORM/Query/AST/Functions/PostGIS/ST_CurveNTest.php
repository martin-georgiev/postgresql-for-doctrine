<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CurveN;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use PHPUnit\Framework\Attributes\Test;

class ST_CurveNTest extends SpatialOperatorTestCase
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
        $this->assertEqualsWithDelta(1.4142, $result[0]['result'], 0.001);
    }

    #[Test]
    public function returns_second_curve_with_measurable_length(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_CURVEN(g.geometry1, 2)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 14';

        $result = $this->executeDqlQuery($dql);
        $this->assertGreaterThan(0, $result[0]['result']);
    }

    #[Test]
    public function returns_null_for_out_of_range_index(): void
    {
        $dql = 'SELECT ST_CURVEN(g.geometry1, 10) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 14';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }

    #[Test]
    public function returns_null_for_linestring(): void
    {
        $dql = 'SELECT ST_CURVEN(g.geometry1, 1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
