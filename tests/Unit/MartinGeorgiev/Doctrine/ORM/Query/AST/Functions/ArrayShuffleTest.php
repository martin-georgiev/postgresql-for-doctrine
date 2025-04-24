<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayShuffle;

class ArrayShuffleTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_SHUFFLE' => ArrayShuffle::class,
            'ARRAY' => Arr::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'from array field' => 'SELECT array_shuffle(c0_.array1) AS sclr_0 FROM ContainsArrays c0_',
            'from literal array' => "SELECT array_shuffle(ARRAY['red', 'green', 'blue']) AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'from array field' => \sprintf('SELECT ARRAY_SHUFFLE(e.array1) FROM %s e', ContainsArrays::class),
            'from literal array' => \sprintf("SELECT ARRAY_SHUFFLE(ARRAY('red', 'green', 'blue')) FROM %s e", ContainsArrays::class),
        ];
    }
}
