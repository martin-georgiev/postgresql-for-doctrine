<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAppend;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class ArrayAppendTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_APPEND' => ArrayAppend::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT array_append(c0_.array, 1989) AS sclr_0 FROM ContainsArray c0_',
            "SELECT array_append(c0_.array, 'country') AS sclr_0 FROM ContainsArray c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            sprintf('SELECT ARRAY_APPEND(e.array, 1989) FROM %s e', ContainsArray::class),
            sprintf("SELECT ARRAY_APPEND(e.array, 'country') FROM %s e", ContainsArray::class),
        ];
    }
}
