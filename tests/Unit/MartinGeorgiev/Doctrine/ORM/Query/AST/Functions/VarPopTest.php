<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\VarPop;

class VarPopTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'VAR_POP' => VarPop::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes population variance of integer field' => 'SELECT var_pop(c0_.integer1) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes population variance of integer field' => \sprintf('SELECT VAR_POP(e.integer1) FROM %s e', ContainsIntegers::class),
        ];
    }
}
