<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MinimumBoundingCircle;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_MinimumBoundingCircleTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_MINIMUMBOUNDINGCIRCLE' => ST_MinimumBoundingCircle::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes bounding circle' => 'SELECT ST_MinimumBoundingCircle(c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
            'computes bounding circle with segments per quarter' => 'SELECT ST_MinimumBoundingCircle(c0_.geometry1, 48) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes bounding circle' => 'SELECT ST_MINIMUMBOUNDINGCIRCLE(g.geometry1) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'computes bounding circle with segments per quarter' => 'SELECT ST_MINIMUMBOUNDINGCIRCLE(g.geometry1, 48) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_MinimumBoundingCircle() requires between 1 and 2 arguments');

        $dql = \sprintf('SELECT ST_MINIMUMBOUNDINGCIRCLE(g.geometry1, 48, 99) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
