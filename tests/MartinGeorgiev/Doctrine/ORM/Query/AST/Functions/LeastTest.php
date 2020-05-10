<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Least;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsIntegers;

class LeastTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LEAST' => Least::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT least(c0_.integer1,c0_.integer2,c0_.integer3) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT LEAST(e.integer1, e.integer2, e.integer3) FROM %s e', ContainsIntegers::class),
        ];
    }
}
