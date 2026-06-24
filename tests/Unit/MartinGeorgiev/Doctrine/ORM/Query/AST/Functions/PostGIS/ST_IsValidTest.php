<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_IsValid;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_IsValidTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ISVALID' => ST_IsValid::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'validates geometry' => 'SELECT ST_IsValid(c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
            'validates geometry with flags' => 'SELECT ST_IsValid(c0_.geometry1, 1) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'validates geometry' => \sprintf('SELECT ST_ISVALID(g.geometry1) FROM %s g', ContainsGeometries::class),
            'validates geometry with flags' => \sprintf('SELECT ST_ISVALID(g.geometry1, 1) FROM %s g', ContainsGeometries::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_IsValid() requires between 1 and 2 arguments');

        $dql = \sprintf('SELECT ST_ISVALID(g.geometry1, 1, 99) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
