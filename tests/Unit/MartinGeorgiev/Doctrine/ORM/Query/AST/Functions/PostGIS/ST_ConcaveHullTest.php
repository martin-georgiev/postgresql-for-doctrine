<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ConcaveHull;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class ST_ConcaveHullTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_CONCAVEHULL' => ST_ConcaveHull::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'with two arguments' => 'SELECT ST_ConcaveHull(c0_.geometry1, 0.99) AS sclr_0 FROM ContainsGeometries c0_',
            'with three arguments (allow_holes)' => "SELECT ST_ConcaveHull(c0_.geometry1, 0.99, 'true') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'with two arguments' => \sprintf('SELECT ST_CONCAVEHULL(g.geometry1, 0.99) FROM %s g', ContainsGeometries::class),
            'with three arguments (allow_holes)' => \sprintf("SELECT ST_CONCAVEHULL(g.geometry1, 0.99, 'true') FROM %s g", ContainsGeometries::class),
        ];
    }

    #[Test]
    public function throws_exception_for_invalid_allow_holes_parameter(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('Invalid boolean value "invalid" provided for ST_ConcaveHull. Must be "true" or "false".');

        $dql = \sprintf("SELECT ST_CONCAVEHULL(g.geometry1, 0.99, 'invalid') FROM %s g", ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_non_constant_boolean_parameter(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('The boolean parameter for ST_ConcaveHull must be a string literal');

        $dql = \sprintf('SELECT ST_CONCAVEHULL(g.geometry1, 0.99, g.geometry1) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
