<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Subdivide;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_SubdivideTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_SUBDIVIDE' => ST_Subdivide::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ST_Subdivide(c0_.geometry1, 256) AS sclr_0 FROM ContainsGeometries c0_',
            'SELECT ST_Subdivide(c0_.geometry1, ?) AS sclr_0 FROM ContainsGeometries c0_',
            'SELECT ST_Subdivide(c0_.geometry1, MIN(1)) AS sclr_0 FROM ContainsGeometries c0_',
            'SELECT ST_Subdivide(c0_.geometry1, 256, 0.5) AS sclr_0 FROM ContainsGeometries c0_',
            'SELECT ST_Subdivide(c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'SELECT ST_SUBDIVIDE(g.geometry1, 256) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_SUBDIVIDE(g.geometry1, :dql_parameter) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_SUBDIVIDE(g.geometry1, MIN(1)) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_SUBDIVIDE(g.geometry1, 256, 0.5) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_SUBDIVIDE(g.geometry1) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }

    #[Test]
    public function throws_exception_for_too_few_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_Subdivide() requires at least 1 argument');

        $dql = \sprintf('SELECT ST_SUBDIVIDE() FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_Subdivide() requires between 1 and 3 arguments');

        $dql = \sprintf('SELECT ST_SUBDIVIDE(g.geometry1, 256, 0.5, 1) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
