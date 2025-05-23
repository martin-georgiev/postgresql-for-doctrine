<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exp;

class ExpTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'EXP' => Exp::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT EXP(11) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT EXP(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT EXP(11) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT EXP(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
