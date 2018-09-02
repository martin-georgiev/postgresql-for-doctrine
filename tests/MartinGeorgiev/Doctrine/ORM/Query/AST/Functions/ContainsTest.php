<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class ContainsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CONTAINS' => Contains::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT (c0_.array @> '{681,1185,1878}') AS sclr_0 FROM ContainsArray c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            sprintf("SELECT CONTAINS(e.array, '{681,1185,1878}') FROM %s e", ContainsArray::class),
        ];
    }
}
