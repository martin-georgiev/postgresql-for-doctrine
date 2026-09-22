<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_HausdorffDistance;
use PHPUnit\Framework\Attributes\Test;

final class ST_HausdorffDistanceTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_HAUSDORFFDISTANCE' => ST_HausdorffDistance::class,
        ];
    }

    #[Test]
    public function returns_the_hausdorff_distance_between_wkt_literals(): void
    {
        $dql = "SELECT ST_HAUSDORFFDISTANCE(ST_GEOMFROMTEXT('POINT(0 0)'), ST_GEOMFROMTEXT('POINT(1 1)')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.4142135623730951, $result[0]['result']);
    }

    #[Test]
    public function returns_the_hausdorff_distance_between_entity_fields(): void
    {
        $dql = 'SELECT ST_HAUSDORFFDISTANCE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.4142135623730951, $result[0]['result']);
    }

    #[Test]
    public function respects_the_densify_fraction_argument(): void
    {
        $dql = 'SELECT ST_HAUSDORFFDISTANCE(g.geometry1, g.geometry2, 0.8) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.4142135623730951, $result[0]['result']);
    }
}
