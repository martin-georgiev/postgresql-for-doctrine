<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MakeValid;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_MakeValidTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_MAKEVALID' => ST_MakeValid::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'repairs geometry' => 'SELECT ST_MakeValid(c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
            'repairs geometry with params' => "SELECT ST_MakeValid(c0_.geometry1, 'method=linework') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'repairs geometry' => \sprintf('SELECT ST_MAKEVALID(g.geometry1) FROM %s g', ContainsGeometries::class),
            'repairs geometry with params' => \sprintf("SELECT ST_MAKEVALID(g.geometry1, 'method=linework') FROM %s g", ContainsGeometries::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_MakeValid() requires between 1 and 2 arguments');

        $dql = \sprintf("SELECT ST_MAKEVALID(g.geometry1, 'method=linework', 'extra') FROM %s g", ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
