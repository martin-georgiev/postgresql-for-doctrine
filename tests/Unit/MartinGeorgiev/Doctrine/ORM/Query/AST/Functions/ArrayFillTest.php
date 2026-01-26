<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayFill;

class ArrayFillTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_FILL' => ArrayFill::class,
            'ARRAY' => Arr::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'fills array with value' => "SELECT array_fill('test', ARRAY[3]) AS sclr_0 FROM ContainsArrays c0_",
            'fills array with integer' => 'SELECT array_fill(42, ARRAY[5]) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'fills array with value' => \sprintf("SELECT ARRAY_FILL('test', ARRAY(3)) FROM %s e", ContainsArrays::class),
            'fills array with integer' => \sprintf('SELECT ARRAY_FILL(42, ARRAY(5)) FROM %s e', ContainsArrays::class),
        ];
    }
}

