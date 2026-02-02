<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayFill;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast;

class ArrayFillTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_FILL' => ArrayFill::class,
            'ARRAY' => Arr::class,
            'CAST' => Cast::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'fills array with integer value' => "SELECT array_fill(42, ARRAY['5']::int[]) AS sclr_0 FROM ContainsArrays c0_",
            'fills array with string value using cast' => "SELECT array_fill(cast('x' as TEXT), ARRAY['3']::int[]) AS sclr_0 FROM ContainsArrays c0_",
            'fills array with boolean value using cast' => "SELECT array_fill(cast('true' as BOOLEAN), ARRAY['2']::int[]) AS sclr_0 FROM ContainsArrays c0_",
            'fills multi-dimensional array' => "SELECT array_fill(11, ARRAY['2', '3']::int[]) AS sclr_0 FROM ContainsArrays c0_",
            'fills array with custom lower bounds' => "SELECT array_fill(7, ARRAY['3']::int[], ARRAY['2']::int[]) AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'fills array with integer value' => \sprintf("SELECT ARRAY_FILL(42, ARRAY('5')) FROM %s e", ContainsArrays::class),
            'fills array with string value using cast' => \sprintf("SELECT ARRAY_FILL(CAST('x' AS TEXT), ARRAY('3')) FROM %s e", ContainsArrays::class),
            'fills array with boolean value using cast' => \sprintf("SELECT ARRAY_FILL(CAST('true' AS BOOLEAN), ARRAY('2')) FROM %s e", ContainsArrays::class),
            'fills multi-dimensional array' => \sprintf("SELECT ARRAY_FILL(11, ARRAY('2', '3')) FROM %s e", ContainsArrays::class),
            'fills array with custom lower bounds' => \sprintf("SELECT ARRAY_FILL(7, ARRAY('3'), ARRAY('2')) FROM %s e", ContainsArrays::class),
        ];
    }
}
