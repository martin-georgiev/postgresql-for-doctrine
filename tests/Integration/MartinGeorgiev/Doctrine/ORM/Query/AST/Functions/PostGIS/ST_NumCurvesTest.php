<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NumCurves;
use PHPUnit\Framework\Attributes\Test;

final class ST_NumCurvesTest extends SpatialOperatorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgisVersion(30500, 'ST_NumCurves');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_NUMCURVES' => ST_NumCurves::class,
        ];
    }

    #[Test]
    public function returns_the_curve_count_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_NUMCURVES(ST_GEOMFROMTEXT('COMPOUNDCURVE((0 0,1 1),CIRCULARSTRING(1 1,2 0,3 1))')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(2, $result[0]['result']);
    }

    #[Test]
    public function returns_the_curve_count_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_NUMCURVES(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 14';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }
}
