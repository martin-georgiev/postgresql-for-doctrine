<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCat;

class ArrayCatTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_CAT' => ArrayCat::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT array_cat(c0_.array1, c0_.array2) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ARRAY_CAT(e.array1, e.array2) FROM %s e', ContainsArrays::class),
        ];
    }
}
