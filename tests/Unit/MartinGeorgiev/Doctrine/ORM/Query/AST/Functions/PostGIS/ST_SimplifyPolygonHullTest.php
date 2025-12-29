<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SimplifyPolygonHull;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class ST_SimplifyPolygonHullTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_SIMPLIFYPOLYGONHULL' => ST_SimplifyPolygonHull::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'with two arguments (outer hull)' => 'SELECT ST_SimplifyPolygonHull(c0_.geometry1, 0.9) AS sclr_0 FROM ContainsGeometries c0_',
            'with three arguments (inner hull)' => "SELECT ST_SimplifyPolygonHull(c0_.geometry1, 0.9, 'false') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'with two arguments (outer hull)' => \sprintf('SELECT ST_SIMPLIFYPOLYGONHULL(g.geometry1, 0.9) FROM %s g', ContainsGeometries::class),
            'with three arguments (inner hull)' => \sprintf("SELECT ST_SIMPLIFYPOLYGONHULL(g.geometry1, 0.9, 'false') FROM %s g", ContainsGeometries::class),
        ];
    }

    #[Test]
    public function throws_exception_for_invalid_is_outer_parameter(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('Invalid boolean value "invalid" provided for ST_SimplifyPolygonHull. Must be "true" or "false".');

        $dql = \sprintf("SELECT ST_SIMPLIFYPOLYGONHULL(g.geometry1, 0.9, 'invalid') FROM %s g", ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_non_constant_boolean_parameter(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('The boolean parameter for ST_SimplifyPolygonHull must be a string literal');

        $dql = \sprintf('SELECT ST_SIMPLIFYPOLYGONHULL(g.geometry1, 0.9, g.geometry1) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
