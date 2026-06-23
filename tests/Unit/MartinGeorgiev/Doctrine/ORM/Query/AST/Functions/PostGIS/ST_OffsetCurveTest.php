<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_OffsetCurve;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_OffsetCurveTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_OFFSETCURVE' => ST_OffsetCurve::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'offsets curve by distance' => 'SELECT ST_OffsetCurve(c0_.geometry1, 1) AS sclr_0 FROM ContainsGeometries c0_',
            'offsets curve with style parameters' => "SELECT ST_OffsetCurve(c0_.geometry1, 1, 'quad_segs=4 join=round') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'offsets curve by distance' => \sprintf('SELECT ST_OFFSETCURVE(g.geometry1, 1) FROM %s g', ContainsGeometries::class),
            'offsets curve with style parameters' => \sprintf("SELECT ST_OFFSETCURVE(g.geometry1, 1, 'quad_segs=4 join=round') FROM %s g", ContainsGeometries::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_OffsetCurve() requires between 2 and 3 arguments');

        $dql = \sprintf("SELECT ST_OFFSETCURVE(g.geometry1, 1, 'quad_segs=4', 99) FROM %s g", ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
