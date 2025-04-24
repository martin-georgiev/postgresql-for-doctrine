<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unnest;

class UnnestTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'UNNEST' => Unnest::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT unnest(c0_.array1) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT UNNEST(e.array1) FROM %s e', ContainsArrays::class),
        ];
    }
}
