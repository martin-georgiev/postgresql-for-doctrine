<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLength;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class ArrayLengthTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_LENGTH' => ArrayLength::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT array_length(c0_.array, 1) AS sclr_0 FROM ContainsArray c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            sprintf('SELECT ARRAY_LENGTH(e.array, 1) FROM %s e', ContainsArray::class),
        ];
    }
}
