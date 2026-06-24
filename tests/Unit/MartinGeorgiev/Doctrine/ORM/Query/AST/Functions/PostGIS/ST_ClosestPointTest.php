<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ClosestPoint;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_ClosestPointTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_CLOSESTPOINT' => ST_ClosestPoint::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'finds closest point between geometries' => 'SELECT ST_ClosestPoint(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
            'finds closest point with use_spheroid' => "SELECT ST_ClosestPoint(c0_.geometry1, c0_.geometry2, 'true') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'finds closest point between geometries' => \sprintf('SELECT ST_CLOSESTPOINT(g.geometry1, g.geometry2) FROM %s g', ContainsGeometries::class),
            'finds closest point with use_spheroid' => \sprintf("SELECT ST_CLOSESTPOINT(g.geometry1, g.geometry2, 'true') FROM %s g", ContainsGeometries::class),
        ];
    }

    #[Test]
    public function throws_exception_for_invalid_boolean(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('Invalid boolean value "invalid" provided for ST_ClosestPoint. Must be "true" or "false".');

        $dql = \sprintf("SELECT ST_CLOSESTPOINT(g.geometry1, g.geometry2, 'invalid') FROM %s g", ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_non_constant_boolean_parameter(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('The boolean parameter for ST_ClosestPoint must be a string literal');

        $dql = \sprintf('SELECT ST_CLOSESTPOINT(g.geometry1, g.geometry2, g.geometry1) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_ClosestPoint() requires between 2 and 3 arguments');

        $dql = \sprintf("SELECT ST_CLOSESTPOINT(g.geometry1, g.geometry2, 'true', 99) FROM %s g", ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
