<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLength;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsArrays;

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
            'SELECT array_length(c0_.array1, 1) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ARRAY_LENGTH(e.array1, 1) FROM %s e', ContainsArrays::class),
        ];
    }
}
