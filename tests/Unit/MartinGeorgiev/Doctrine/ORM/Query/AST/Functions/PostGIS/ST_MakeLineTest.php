<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MakeLine;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_MakeLineTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_MAKELINE' => ST_MakeLine::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates line as aggregate' => 'SELECT ST_MakeLine(c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
            'creates line from two geometries' => 'SELECT ST_MakeLine(c0_.geometry1, c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates line as aggregate' => \sprintf('SELECT ST_MAKELINE(g.geometry1) FROM %s g', ContainsGeometries::class),
            'creates line from two geometries' => \sprintf('SELECT ST_MAKELINE(g.geometry1, g.geometry2) FROM %s g', ContainsGeometries::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_MakeLine() requires between 1 and 2 arguments');

        $dql = \sprintf('SELECT ST_MAKELINE(g.geometry1, g.geometry2, g.geometry1) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
