<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class ArrayRemoveTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_REMOVE' => ArrayRemove::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT array_remove(c0_.array, 1944) AS sclr_0 FROM ContainsArray c0_',
            "SELECT array_remove(c0_.array, 'peach') AS sclr_0 FROM ContainsArray c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ARRAY_REMOVE(e.array, 1944) FROM %s e', ContainsArray::class),
            \sprintf("SELECT ARRAY_REMOVE(e.array, 'peach') FROM %s e", ContainsArray::class),
        ];
    }
}
