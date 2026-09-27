<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Scale;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_ScaleTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_SCALE' => ST_Scale::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ST_Scale(c0_.geometry1, 2, 2) AS sclr_0 FROM ContainsGeometries c0_',
            'SELECT ST_Scale(c0_.geometry1, ?, ?) AS sclr_0 FROM ContainsGeometries c0_',
            'SELECT ST_Scale(c0_.geometry1, MIN(1), MIN(1)) AS sclr_0 FROM ContainsGeometries c0_',
            'SELECT ST_Scale(c0_.geometry1, 2, 2, 1) AS sclr_0 FROM ContainsGeometries c0_',
            "SELECT ST_Scale(c0_.geometry1, 'POINT(2 3)') AS sclr_0 FROM ContainsGeometries c0_",
            "SELECT ST_Scale(c0_.geometry1, 'POINT(2 3)', 'POINT(1 1)') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'SELECT ST_SCALE(g.geometry1, 2, 2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_SCALE(g.geometry1, :dql_parameter1, :dql_parameter2) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_SCALE(g.geometry1, MIN(1), MIN(1)) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'SELECT ST_SCALE(g.geometry1, 2, 2, 1) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            "SELECT ST_SCALE(g.geometry1, 'POINT(2 3)') FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g",
            "SELECT ST_SCALE(g.geometry1, 'POINT(2 3)', 'POINT(1 1)') FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g",
        ];
    }

    #[Test]
    public function throws_exception_for_too_few_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_Scale() requires at least 2 arguments');

        $dql = \sprintf('SELECT ST_SCALE(g.geometry1) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_Scale() requires between 2 and 4 arguments');

        $dql = \sprintf('SELECT ST_SCALE(g.geometry1, 2, 2, 1, 1) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
