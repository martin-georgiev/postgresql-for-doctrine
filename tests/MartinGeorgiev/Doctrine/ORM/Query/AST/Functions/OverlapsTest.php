<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

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
            "SELECT (c0_.array && '{681,1185,1878}') AS sclr_0 FROM ContainsArray c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            sprintf("SELECT OVERLAPS(e.array, '{681,1185,1878}') FROM %s e", ContainsArray::class),
        ];
    }
}
