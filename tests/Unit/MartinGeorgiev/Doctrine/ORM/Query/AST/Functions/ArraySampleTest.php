<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArraySample;

class ArraySampleTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_SAMPLE' => ArraySample::class,
            'ARRAY' => Arr::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'from array field' => 'SELECT array_sample(c0_.textArray, 3) AS sclr_0 FROM ContainsArrays c0_',
            'from literal array' => "SELECT array_sample(ARRAY['red', 'green', 'blue', 'yellow'], 2) AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'from array field' => \sprintf('SELECT ARRAY_SAMPLE(e.textArray, 3) FROM %s e', ContainsArrays::class),
            'from literal array' => \sprintf("SELECT ARRAY_SAMPLE(ARRAY('red', 'green', 'blue', 'yellow'), 2) FROM %s e", ContainsArrays::class),
        ];
    }
}

