<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineLocatePoint;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_LineLocatePointTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LINELOCATEPOINT' => ST_LineLocatePoint::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'locates point on line' => 'SELECT ST_LineLocatePoint(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
            'locates point on line with use_spheroid' => "SELECT ST_LineLocatePoint(c0_.geometry1, c0_.geometry2, 'true') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'locates point on line' => \sprintf('SELECT ST_LINELOCATEPOINT(g.geometry1, g.geometry2) FROM %s g', ContainsGeometries::class),
            'locates point on line with use_spheroid' => \sprintf("SELECT ST_LINELOCATEPOINT(g.geometry1, g.geometry2, 'true') FROM %s g", ContainsGeometries::class),
        ];
    }

    #[Test]
    public function throws_exception_for_invalid_boolean(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('Invalid boolean value "invalid" provided for ST_LineLocatePoint. Must be "true" or "false".');

        $dql = \sprintf("SELECT ST_LINELOCATEPOINT(g.geometry1, g.geometry2, 'invalid') FROM %s g", ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_non_constant_boolean_parameter(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('The boolean parameter for ST_LineLocatePoint must be a string literal');

        $dql = \sprintf('SELECT ST_LINELOCATEPOINT(g.geometry1, g.geometry2, g.geometry1) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_LineLocatePoint() requires between 2 and 3 arguments');

        $dql = \sprintf("SELECT ST_LINELOCATEPOINT(g.geometry1, g.geometry2, 'true', 99) FROM %s g", ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
