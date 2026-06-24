<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_VoronoiPolygons;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_VoronoiPolygonsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_VORONOIPOLYGONS' => ST_VoronoiPolygons::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates polygons from geometry' => 'SELECT ST_VoronoiPolygons(c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
            'creates polygons with tolerance' => 'SELECT ST_VoronoiPolygons(c0_.geometry1, 0.0) AS sclr_0 FROM ContainsGeometries c0_',
            'creates polygons with tolerance and extend_to' => 'SELECT ST_VoronoiPolygons(c0_.geometry1, 0.0, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates polygons from geometry' => \sprintf('SELECT ST_VORONOIPOLYGONS(g.geometry1) FROM %s g', ContainsGeometries::class),
            'creates polygons with tolerance' => \sprintf('SELECT ST_VORONOIPOLYGONS(g.geometry1, 0.0) FROM %s g', ContainsGeometries::class),
            'creates polygons with tolerance and extend_to' => \sprintf('SELECT ST_VORONOIPOLYGONS(g.geometry1, 0.0, g.geometry2) FROM %s g', ContainsGeometries::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_VoronoiPolygons() requires between 1 and 3 arguments');

        $dql = \sprintf('SELECT ST_VORONOIPOLYGONS(g.geometry1, 0.0, g.geometry2, 99) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
