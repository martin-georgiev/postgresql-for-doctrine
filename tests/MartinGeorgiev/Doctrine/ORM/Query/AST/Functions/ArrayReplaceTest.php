<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace;

class ArrayReplaceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_REPLACE' => ArrayReplace::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT array_replace(c0_.array1, 1939, 1957) AS sclr_0 FROM ContainsArrays c0_',
            "SELECT array_replace(c0_.array1, 'green', 'mint') AS sclr_0 FROM ContainsArrays c0_",
            "SELECT array_replace(c0_.array1, 'green', ?) AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ARRAY_REPLACE(e.array1, 1939, 1957) FROM %s e', ContainsArrays::class),
            \sprintf("SELECT ARRAY_REPLACE(e.array1, 'green', 'mint') FROM %s e", ContainsArrays::class),
            \sprintf("SELECT ARRAY_REPLACE(e.array1, 'green', :dql_parameter) FROM %s e", ContainsArrays::class),
        ];
    }
}
