<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NumCurves;
use PHPUnit\Framework\Attributes\Test;

class ST_NumCurvesTest extends SpatialOperatorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgisVersion(30500, 'ST_NumCurves');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ST_NUMCURVES' => ST_NumCurves::class,
        ];
    }

    #[Test]
    public function returns_curve_count_for_compound_curve_with_three_components(): void
    {
        $dql = 'SELECT ST_NUMCURVES(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 14';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }

    #[Test]
    public function returns_curve_count_for_compound_curve_with_two_components(): void
    {
        $dql = 'SELECT ST_NUMCURVES(g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 14';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(2, $result[0]['result']);
    }

    #[Test]
    public function returns_null_for_linestring(): void
    {
        $dql = 'SELECT ST_NUMCURVES(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
