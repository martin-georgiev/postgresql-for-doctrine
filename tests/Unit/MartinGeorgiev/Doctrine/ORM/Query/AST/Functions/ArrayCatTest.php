<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

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
            'concatenates two arrays' => 'SELECT array_cat(c0_.smallintArray, c0_.integerArray) AS sclr_0 FROM ContainsArrays c0_',
            'concatenates with parameter' => 'SELECT array_cat(c0_.textArray, ?) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'concatenates two arrays' => \sprintf('SELECT ARRAY_CAT(e.smallintArray, e.integerArray) FROM %s e', ContainsArrays::class),
            'concatenates with parameter' => \sprintf('SELECT ARRAY_CAT(e.textArray, :parameter) FROM %s e', ContainsArrays::class),
        ];
    }
}
