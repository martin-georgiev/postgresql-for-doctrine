<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineInterpolatePoint;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_LineInterpolatePointTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LINEINTERPOLATEPOINT' => ST_LineInterpolatePoint::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'interpolates point along line' => 'SELECT ST_LineInterpolatePoint(c0_.geometry1, 0.5) AS sclr_0 FROM ContainsGeometries c0_',
            'interpolates point with use_spheroid' => "SELECT ST_LineInterpolatePoint(c0_.geometry1, 0.5, 'true') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'interpolates point along line' => \sprintf('SELECT ST_LINEINTERPOLATEPOINT(g.geometry1, 0.5) FROM %s g', ContainsGeometries::class),
            'interpolates point with use_spheroid' => \sprintf("SELECT ST_LINEINTERPOLATEPOINT(g.geometry1, 0.5, 'true') FROM %s g", ContainsGeometries::class),
        ];
    }

    #[Test]
    public function throws_exception_for_invalid_boolean(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('Invalid boolean value "invalid" provided for ST_LineInterpolatePoint. Must be "true" or "false".');

        $dql = \sprintf("SELECT ST_LINEINTERPOLATEPOINT(g.geometry1, 0.5, 'invalid') FROM %s g", ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_non_constant_boolean_parameter(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('The boolean parameter for ST_LineInterpolatePoint must be a string literal');

        $dql = \sprintf('SELECT ST_LINEINTERPOLATEPOINT(g.geometry1, 0.5, g.geometry1) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_LineInterpolatePoint() requires between 2 and 3 arguments');

        $dql = \sprintf("SELECT ST_LINEINTERPOLATEPOINT(g.geometry1, 0.5, 'true', 99) FROM %s g", ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
