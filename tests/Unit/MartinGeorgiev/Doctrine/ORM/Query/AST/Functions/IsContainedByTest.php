<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy;

class IsContainedByTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'IS_CONTAINED_BY' => IsContainedBy::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks if left array is contained by right array' => "SELECT (c0_.textArray <@ '{681,1185,1878}') AS sclr_0 FROM ContainsArrays c0_",
            'checks if geometry is contained by another' => 'SELECT (c0_.geometry1 <@ c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
            'checks if geometry is contained by literal' => "SELECT (c0_.geometry1 <@ 'POLYGON((0 0, 2 2, 4 4, 0 0))') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if left array is contained by right array' => \sprintf("SELECT IS_CONTAINED_BY(e.textArray, '{681,1185,1878}') FROM %s e", ContainsArrays::class),
            'checks if geometry is contained by another' => \sprintf('SELECT IS_CONTAINED_BY(e.geometry1, e.geometry2) FROM %s e', ContainsGeometries::class),
            'checks if geometry is contained by literal' => \sprintf("SELECT IS_CONTAINED_BY(e.geometry1, 'POLYGON((0 0, 2 2, 4 4, 0 0))') FROM %s e", ContainsGeometries::class),
        ];
    }
}
