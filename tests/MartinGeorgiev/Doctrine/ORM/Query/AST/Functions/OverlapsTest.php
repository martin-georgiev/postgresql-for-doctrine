<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsArrays;

class OverlapsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'OVERLAPS' => Overlaps::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT (c0_.array1 && '{681,1185,1878}') AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT OVERLAPS(e.array1, '{681,1185,1878}') FROM %s e", ContainsArrays::class),
        ];
    }
}
