<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArrays;

class ArrayToStringTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_TO_STRING' => ArrayToString::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT array_to_string(c0_.array1, '; ') AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT ARRAY_TO_STRING(e.array1, '; ') FROM %s e", ContainsArrays::class),
        ];
    }
}
